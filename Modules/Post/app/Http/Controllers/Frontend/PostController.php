<?php

namespace Modules\Post\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Category\Models\Category;
use Modules\Post\Models\Package;
use Modules\Post\Models\Post;
use Modules\Post\Models\PostType;
use Modules\Post\Services\PostPaymentService;
use Modules\Post\Services\PostService;
use Modules\Shipping\Models\ShippingCity;
use Modules\Shipping\Models\ShippingState;

class PostController extends Controller
{
    public function __construct(
        protected PostService $postService,
        protected PostPaymentService $paymentService
    ) {}

    public function index(Request $request)
    {
        $categories = Category::where('status', 'active')->orderBy('position')->get();
        $postTypes = PostType::where('status', true)->orderBy('id')->get();
        $locations = ShippingState::withCount('cities')
            ->with(['cities' => function ($query) {
                $query->orderBy('name_ar');
            }])
            ->orderBy('name_ar')
            ->get();

        $query = Post::with(['attachments', 'category', 'postType', 'city.state', 'package'])
            ->active();

        // Filter by search query
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%'.$searchTerm.'%')
                    ->orWhere('description', 'like', '%'.$searchTerm.'%');
            });
        }

        // Filter by post type
        $selectedPostType = null;
        if ($request->filled('type')) {
            $selectedPostType = $postTypes->where('slug', $request->type)->first();
            if ($selectedPostType) {
                $query->where('post_type_id', $selectedPostType->id);
            }
        }

        // Filter by category
        $selectedCategory = null;
        if ($request->filled('category')) {
            $selectedCategory = $categories->where('slug', $request->category)->first();
            if ($selectedCategory) {
                $query->where('category_id', $selectedCategory->id);
            }
        }

        // Handle new locations[] array format from homepage
        $selectedLocations = [];
        $selectedStates = [];
        $selectedCities = [];

        if ($request->filled('locations')) {
            $locationValues = $request->input('locations', []);

            foreach ($locationValues as $loc) {
                if (str_starts_with($loc, 'state_')) {
                    $stateId = (int) str_replace('state_', '', $loc);
                    $selectedStates[] = $stateId;
                } elseif (str_starts_with($loc, 'city_')) {
                    $cityId = (int) str_replace('city_', '', $loc);
                    $selectedCities[] = $cityId;
                }
            }

            if (! empty($selectedStates) || ! empty($selectedCities)) {
                $query->where(function ($q) use ($selectedStates, $selectedCities) {
                    if (! empty($selectedStates)) {
                        $q->whereIn('state_id', $selectedStates);
                    }
                    if (! empty($selectedCities)) {
                        $q->orWhereIn('city_id', $selectedCities);
                    }
                });
            }
        }

        // Legacy: Filter by location (state)
        $selectedLocation = null;
        if ($request->filled('location') && ! $request->filled('locations')) {
            $selectedLocation = ShippingState::find($request->location);
            if ($selectedLocation) {
                $query->where('state_id', $selectedLocation->id);
            }
        }

        // Legacy: Filter by city
        $selectedCity = null;
        if ($request->filled('city') && ! $request->filled('locations')) {
            $selectedCity = ShippingCity::find($request->city);
            if ($selectedCity) {
                $query->where('city_id', $selectedCity->id);
            }
        }

        // Order by priority: paid/top posts first, then by date
        $query->orderByPriority();

        $perPage = 20;
        $totalCount = $query->count();
        $posts = $query->paginate($perPage);

        // Build page title
        $pageTitle = __('frontend.listings.title');

        if ($selectedCategory && $selectedPostType) {
            $pageTitle = $selectedCategory->name.' '.$selectedPostType->name;
        } elseif ($selectedCategory) {
            $pageTitle = $selectedCategory->name;
        } elseif ($selectedPostType) {
            $pageTitle = $selectedPostType->name;
        }

        // Handle AJAX request for load more
        if ($request->ajax() || $request->wantsJson()) {
            $html = '';
            foreach ($posts as $post) {
                $html .= view('post::frontend.partials.post-card-horizontal', compact('post'))->render();
            }

            return response()->json([
                'html' => $html,
                'hasMore' => $posts->hasMorePages(),
                'count' => $posts->count(),
                'total' => $totalCount,
                'currentPage' => $posts->currentPage(),
                'lastPage' => $posts->lastPage(),
            ]);
        }

        return view('post::frontend.index', compact(
            'posts',
            'categories',
            'postTypes',
            'locations',
            'selectedCategory',
            'selectedPostType',
            'selectedLocation',
            'selectedCity',
            'selectedStates',
            'selectedCities',
            'pageTitle',
            'totalCount'
        ));
    }

    public function show(string $slug)
    {
        // Try to find by UUID (slug is actually uuid)
        $post = Post::with(['attachments', 'category', 'postType', 'city.state', 'user'])
            ->where('uuid', $slug)
            // ->active()
            ->firstOrFail();

        $post->update(['views_count' => $post->views_count + 1]);

        // Get related posts - same category, type, and city
        $relatedPosts = Post::with(['attachments', 'category', 'postType', 'state', 'city', 'package'])
            ->active()
            ->where('posts.id', '!=', $post->id)
            ->where('posts.category_id', $post->category_id)
            ->where('posts.post_type_id', $post->post_type_id)
            ->where('posts.city_id', $post->city_id)
            ->orderByPriority()
            ->limit(30)
            ->get();

        // If not enough results, get more with just category and type
        if ($relatedPosts->count() < 10) {
            $existingIds = $relatedPosts->pluck('id')->toArray();
            $existingIds[] = $post->id;

            $moreRelated = Post::with(['attachments', 'category', 'postType', 'state', 'city', 'package'])
                ->active()
                ->whereNotIn('posts.id', $existingIds)
                ->where('posts.category_id', $post->category_id)
                ->where('posts.post_type_id', $post->post_type_id)
                ->orderByPriority()
                ->limit(30 - $relatedPosts->count())
                ->get();

            $relatedPosts = $relatedPosts->concat($moreRelated);
        }

        return view('post::frontend.show', compact('post', 'relatedPosts'));
    }

    public function create()
    {
        $categories = Category::where('status', 'active')->orderBy('position')->get();
        $postTypes = PostType::where('status', true)->orderBy('id')->get();
        $locations = ShippingState::with('cities')->orderBy('name_ar')->get();
        $packages = Package::where('status', true)
            ->orderBy('is_free', 'desc')
            ->orderBy('top_days', 'desc')
            ->orderBy('price')
            ->get();

        // Get free package and calculate remaining credits
        $freePackage = Package::getActiveFreePackage();
        $freeCreditsRemaining = 0;
        $freeCreditsTotal = 0;

        if ($freePackage && auth('admin')->check()) {
            $user = auth('admin')->user();
            $freeCreditsTotal = $freePackage->free_credits_per_user ?? 0;

            // Count how many posts the user has created with the free package
            $usedFreeCredits = Post::where('user_id', $user->id)
                ->where('package_id', $freePackage->id)
                ->count();

            $freeCreditsRemaining = max(0, $freeCreditsTotal - $usedFreeCredits);
        }

        return view('post::frontend.create', compact(
            'categories',
            'postTypes',
            'locations',
            'packages',
            'freePackage',
            'freeCreditsRemaining',
            'freeCreditsTotal'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'category_id' => 'required|exists:categories,id',
            'post_type_id' => 'required|exists:post_types,id',
            'package_id' => 'required|exists:packages,id',
            'city_id' => 'required|exists:shipping_cities,id',
            'state_id' => 'required|exists:shipping_states,id',
            'mobile_number' => 'required|string|max:20',
            'images.*' => 'nullable|image|max:5120',
            'price' => 'nullable|numeric|min:0',
        ]);

        $package = Package::where('id', $validated['package_id'])->firstOrFail();
        $days = $package->period_days;
        $user = auth('admin')->user();

        // Validate free package credits
        if ($package->is_free) {
            $freeCreditsTotal = $package->free_credits_per_user ?? 0;
            $usedFreeCredits = Post::where('user_id', $user->id)
                ->where('package_id', $package->id)
                ->count();

            if ($usedFreeCredits >= $freeCreditsTotal) {
                return response()->json([
                    'success' => false,
                    'message' => __('frontend.new_listing.no_free_credits'),
                ], 422);
            }
        }

        $start_date = now();
        $isPaidPackage = (float) $package->price > 0;

        // Create the post
        $post = Post::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'post_type_id' => $validated['post_type_id'],
            'city_id' => $validated['city_id'],
            'state_id' => $validated['state_id'],
            'price' => $validated['price'],
            'package_id' => $validated['package_id'],
            'mobile_number' => '+965'.preg_replace('/[^0-9]/', '', $validated['mobile_number']),
            'user_id' => $user->id,
            'is_paid' => !$isPaidPackage, // Free packages are marked as paid immediately
            'status' => $isPaidPackage ? 'awaiting_payment' : 'pending',
            'start_date' => $start_date,
            'end_date' => (clone $start_date)->addDays($days),
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('posts/'.$post->id, 'public');
                $post->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $image->getClientOriginalName(),
                    'position' => $index,
                ]);
            }
        }

        // Handle payment for paid packages
        if ($isPaidPackage) {
            try {
                $callbackUrl = route('frontend.posts.payment.callback', ['uuid' => $post->uuid]);

                $payment = $this->paymentService->createMyFatoorahPayment($post, [
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile' => $validated['mobile_number'],
                ], $callbackUrl);

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'payment_required' => true,
                        'message' => __('frontend.new_listing.payment_required'),
                        'payment_url' => $payment->invoice_url,
                        'payment' => [
                            'amount' => $package->price,
                            'currency' => 'KWD',
                            'invoice_url' => $payment->invoice_url,
                            'ref_number' => $payment->ref_number,
                        ],
                    ]);
                }

                // Redirect to MyFatoorah payment page
                return redirect($payment->invoice_url);

            } catch (\Exception $e) {
                Log::error('Frontend MyFatoorah payment creation failed', [
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('frontend.new_listing.payment_error'),
                    ], 500);
                }

                return redirect()
                    ->route('frontend.posts.create')
                    ->with('error', __('frontend.new_listing.payment_error'));
            }
        }

        // For free packages, notify admins and return success
        $this->postService->notifyNewPost($post, $user);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('frontend.new_listing.success_title'),
                'redirect_url' => route('frontend.account.dashboard'),
            ]);
        }

        return redirect()
            ->route('frontend.posts.show', $post->uuid)
            ->with('success', __('frontend.new_listing.success_title'));
    }

    /**
     * Show the form for editing a post
     */
    public function edit(string $uuid)
    {
        $post = Post::with(['attachments', 'category', 'postType'])
            ->where('uuid', $uuid)
            ->where('user_id', auth('admin')->id())
            ->firstOrFail();

        $categories = Category::where('status', 'active')->orderBy('position')->get();
        $postTypes = PostType::where('status', true)->orderBy('id')->get();
        $locations = ShippingState::with('cities')->orderBy('name_ar')->get();
        $packages = Package::where('status', true)
            ->orderBy('is_free', 'desc')
            ->orderBy('top_days', 'desc')
            ->orderBy('price')
            ->get();

        return view('post::frontend.edit', compact('post', 'categories', 'postTypes', 'locations', 'packages'));
    }

    /**
     * Update the specified post
     */
    public function update(Request $request, string $uuid)
    {
        $post = Post::where('uuid', $uuid)
            ->where('user_id', auth('admin')->id())
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'category_id' => 'required|exists:categories,id',
            'post_type_id' => 'required|exists:post_types,id',
            'city_id' => 'required|exists:shipping_cities,id',
            'state_id' => 'required|exists:shipping_states,id',
            'mobile_number' => 'required|string|max:20',
            'images.*' => 'nullable|image|max:5120',
            'price' => 'nullable|numeric|min:0',
        ]);

        $post->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'post_type_id' => $validated['post_type_id'],
            'city_id' => $validated['city_id'],
            'state_id' => $validated['state_id'],
            'price' => $validated['price'],
            'mobile_number' => '+965'.preg_replace('/[^0-9]/', '', $validated['mobile_number']),
            'status' => 'pending', // Reset to pending after edit
        ]);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('posts/'.$post->id, 'public');
                $post->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $image->getClientOriginalName(),
                    'position' => $post->attachments()->count() + $index,
                ]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الإعلان بنجاح',
            ]);
        }

        return redirect()
            ->route('frontend.posts.show', $post->uuid)
            ->with('success', 'تم تحديث الإعلان بنجاح');
    }

    /**
     * Delete the specified post
     */
    public function destroy(string $uuid)
    {
        $post = Post::where('uuid', $uuid)
            ->where('user_id', auth('admin')->id())
            ->firstOrFail();

        // Delete attachments from storage
        foreach ($post->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $post->attachments()->delete();
        $post->delete();

        return response()->json([
            'success' => true,
            'message' => __('frontend.post_deleted_successfully'),
            'redirect_url' => route('frontend.account.dashboard'),
        ]);
    }

    /**
     * Stop publishing the specified post
     */
    public function stop(string $uuid)
    {
        $post = Post::where('uuid', $uuid)
            ->where('user_id', auth('admin')->id())
            ->firstOrFail();

        $post->update([
            'status' => 'stopped',
            // 'end_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم ايقاف نشر الأعلان بنجاح',
        ]);
    }

    /**
     * Resume publishing the specified post
     */
    public function resume(string $uuid)
    {
        $post = Post::where('uuid', $uuid)
            ->where('user_id', auth('admin')->id())
            ->firstOrFail();

        // Check if post was stopped
        if ($post->status !== 'stopped') {
            return response()->json([
                'success' => false,
                'message' => 'هذا الإعلان ليس متوقفاً',
            ], 400);
        }

        $post->update([
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة تفعيل نشر الإعلان بنجاح',
        ]);
    }

    public function deleteAttachment(Post $post, $attachmentId)
    {
        // Check if the user owns this post
        if ($post->user_id !== auth('admin')->id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف هذه الصورة',
            ], 403);
        }

        $attachment = $post->attachments()->findOrFail($attachmentId);

        // Delete file from storage
        if ($attachment->file_path) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الصورة بنجاح',
        ]);
    }

    /**
     * Handle MyFatoorah payment callback
     */
    public function paymentCallback(Request $request, string $uuid)
    {
        $post = Post::where('uuid', $uuid)->firstOrFail();
        $payload = $request->all();

        try {
            $updatedPost = $this->paymentService->handleCallback($payload);

            if ($updatedPost && $updatedPost->is_paid) {
                // Payment successful
                return redirect()
                    ->route('frontend.posts.show', $post->uuid)
                    ->with('success', __('frontend.payment.success'));
            }

            // Payment failed
            return redirect()
                ->route('frontend.posts.show', $post->uuid)
                ->with('error', __('frontend.payment.failed'));

        } catch (\Exception $e) {
            Log::error('Frontend payment callback error', [
                'post_id' => $post->id,
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return redirect()
                ->route('frontend.posts.show', $post->uuid)
                ->with('error', __('frontend.payment.error'));
        }
    }

    /**
     * Retry payment for a post
     */
    public function retryPayment(Request $request, string $uuid)
    {
        $post = Post::where('uuid', $uuid)
            ->where('user_id', auth('admin')->id())
            ->firstOrFail();

        // Check if post actually needs payment
        if ($post->is_paid) {
            return response()->json([
                'success' => false,
                'message' => __('frontend.payment.already_paid'),
            ], 400);
        }

        $package = $post->package;
        if ((float) $package->price <= 0) {
            return response()->json([
                'success' => false,
                'message' => __('frontend.payment.free_package'),
            ], 400);
        }

        try {
            $user = auth('admin')->user();
            $callbackUrl = route('frontend.posts.payment.callback', ['uuid' => $post->uuid]);

            $payment = $this->paymentService->createMyFatoorahPayment($post, [
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $post->mobile_number,
            ], $callbackUrl);

            return response()->json([
                'success' => true,
                'payment_url' => $payment->invoice_url,
                'payment' => [
                    'amount' => $package->price,
                    'currency' => 'KWD',
                    'invoice_url' => $payment->invoice_url,
                    'ref_number' => $payment->ref_number,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Frontend payment retry failed', [
                'post_id' => $post->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('frontend.payment.creation_failed'),
            ], 500);
        }
    }
}
