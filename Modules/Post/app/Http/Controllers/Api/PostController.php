<?php

namespace Modules\Post\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Post\Http\Requests\Api\StorePostRequest;
use Modules\Post\Http\Resources\MyPostSummaryResource;
use Modules\Post\Http\Resources\PostSummaryResource;
use Modules\Post\Models\Package;
use Modules\Post\Models\Post;
use Modules\Post\Models\Skill;
use Modules\Post\Services\PostPaymentService;
use Modules\Post\Services\PostQueryBuilder;
use Modules\Post\Services\PostService;
use Illuminate\Http\Request;
use Modules\Post\Http\Resources\PostDetailsResource;

class PostController extends Controller
{
    use ApiResponse;

    public function __construct(protected PostService $postService,protected PostPaymentService $paymentService) { }



    /**
     * List posts with filters.
     */
    public function index(Request $request): JsonResponse
    {
        // Accept both 'limit' and 'per_page' parameters
        $perPage = (int) ($request->input('limit') ?? $request->input('per_page') ?? 10);
        $perPage = max(1, min($perPage, 50));

        if ($request->input('show_in_home') == 1) {
            $perPage = 6;
        }

        $posts = PostQueryBuilder::make($request)
            ->applyFilters()
            ->build()
            ->orderByPriority()
            ->paginate($perPage);

        return $this->successResponse(
            data: [
                'posts' => PostSummaryResource::collection($posts)->resolve(),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'total' => $posts->total(),
                    'per_page' => $posts->perPage(),
                ],
            ],
            message: __('post::post.messages.retrieved')
        );
    }



    public function store(StorePostRequest $request): JsonResponse
    {
        // Check for free package limits
        $package = Package::findOrFail($request->input('package_id'));
        $user = $request->user();

        if ($package->is_free) {
            $usedFreeCredits = Post::where('user_id', $user->id)
                ->where('package_id', $package->id)
                ->count();
            
            if ($usedFreeCredits >= ($package->free_credits_per_user ?? 0)) {
                return $this->errorResponse(
                    __('post::post.messages.free_limit_reached'),
                    [],
                    403
                );
            }
        }

        $post = DB::transaction(function () use ($request, $package) {
            $data = $request->validated();
            $user = $request->user();

            // Set same content for both Arabic and English in title and description
            if (isset($data['title'])) {
                $data['title'] = ['en' => $data['title'], 'ar' => $data['title']];
            }
            if (isset($data['description'])) {
                $data['description'] = ['en' => $data['description'], 'ar' => $data['description']];
            }

            // Handle Cover Image
            // if ($request->hasFile('cover_image')) {
            //     $data['cover_image'] = $request->file('cover_image')->store('posts/covers', 'public');
            // }

            // Calculate dates based on package (using passed package)
            // $package = Package::findOrFail($data['package_id']);

            $data['start_date'] = now();
            $data['end_date'] = now()->addDays($package->period_days);
            $data['user_id'] = $user->id;
            
            // Set initial status and payment flag based on package price
            $isPaidPackage = (float) $package->price > 0;
            $data['is_paid'] = !$isPaidPackage; // Free packages are marked as paid immediately
            $data['status'] = $isPaidPackage ? 'awaiting_payment' : 'pending';

            // Create Post
            $post = Post::create($data);

            // Handle Skills
            // if (! empty($data['skills'])) {
            //     $skillIds = [];
            //     foreach ($data['skills'] as $skillName) {
            //         $skill = Skill::firstOrCreate(
            //             ['slug' => str()->slug($skillName)],
            //             ['name' => ['en' => $skillName, 'ar' => $skillName]]
            //         );
            //         $skillIds[] = $skill->id;
            //     }
            //     $post->skills()->sync($skillIds);
            // }

            // Handle Attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $title => $file) {
                    $path = $file->store('posts/attachments', 'public');
                    $post->attachments()->create([
                        // 'title' => is_string($title) ? $title : $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientOriginalExtension(),
                    ]);
                }
            }

            return $post;
        });

        $user = $request->user();
        $package = $post->package;

        $isPaidPackage = (float) $package->price > 0;

        // Handle payment for paid packages using MyFatoorah
        if ($isPaidPackage) {
            try {
                $callbackUrl = route('api.posts.payment.callback', ['post' => $post->uuid]);

                $payment = $this->paymentService->createMyFatoorahPayment($post, [
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                ], $callbackUrl);

                return $this->successResponse(
                    data: [
                        'post' => (new MyPostSummaryResource($post))->resolve(),
                        'payment' => [
                            'required' => true,
                            'amount' => $package->price,
                            'currency' => 'KWD',
                            'invoice_url' => $payment->invoice_url,
                            'ref_number' => $payment->ref_number,
                        ],
                    ],
                    message: __('post::post.messages.payment_required'),
                    status: 201
                );
            } catch (\Exception $e) {
                // Log the actual error for debugging
                \Log::error('Post MyFatoorah payment creation failed', [
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                // If payment creation fails, still return the post but notify the user
                return $this->successResponse(
                    data: [
                        'post' => (new MyPostSummaryResource($post))->resolve(),
                        'payment' => [
                            'required' => true,
                            'error' => true,
                            'message' => __('post::post.messages.payment_creation_failed'),
                            'debug' => config('app.debug') ? $e->getMessage() : null,
                        ],
                    ],
                    message: __('post::post.messages.created_payment_pending'),
                    status: 201
                );
            }
        }

        // For free packages, notify admins and return success
        $this->postService->notifyNewPost($post, $user);

        return $this->successResponse(
            data: ['post' => (new PostDetailsResource($post))->resolve()],
            message: __('post::post.messages.created'),
            status: 201
        );
    }



    public function show(Post $post): JsonResponse
    {
        $post->load(['user', 'category', 'postType', 'city', 'state', 'attachments', 'package']);

        $post->update(['views_count' => $post->views_count + 1]);

        // Get related posts
        // Get related posts
        // Get related posts - same category, type, and city
        $relatedPosts = Post::with(['attachments', 'category', 'postType', 'city', 'state', 'package'])
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

        $relatedPostsCount = $relatedPosts->count();

        return $this->successResponse(
            data: [
                'post' => (new PostDetailsResource($post))->resolve(),
                'related_posts' => PostSummaryResource::collection($relatedPosts)->resolve(),
                'related_posts_count' => $relatedPostsCount,
            ],
            message: __('post::post.messages.post_retrieved')
        );
    }



    public function myPosts(Request $request): JsonResponse
    {
        $query = Post::query()
            ->where('user_id', $request->user()->id)
            ->with(['category', 'postType', 'package']);

        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'pending') {
                $query->whereIn('status', ['pending', 'rejected']);
            } else {
                $query->where('status', $status);
            }
        }

        $posts = $query->latest()->paginate(10);

        return $this->successResponse(
            data: [
                'posts' => \Modules\Post\Http\Resources\MyPostSummaryResource::collection($posts)->resolve(),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'total' => $posts->total(),
                    'per_page' => $posts->perPage(),
                ],
            ],
            message: __('post::post.messages.my_posts_retrieved')
        );
    }

    public function myPost(Request $request, Post $post): JsonResponse
    {
        // Use policy for authorization
        Gate::authorize('view', $post);

        $post->load(['user', 'category', 'postType', 'city', 'skills', 'attachments', 'package']);

        return $this->successResponse(
            data: ['post' => (new MyPostSummaryResource($post))->resolve()],
            message: __('post::post.messages.post_retrieved')
        );
    }

    public function update(StorePostRequest $request, $uuid): JsonResponse
    {
        // Use policy for authorization
        // Gate::authorize('update', $post);
        $post = Post::where('uuid',$uuid)->where('user_id', $request->user()->id)->first();
        
        if($post == null) {
            return $this->errorResponse(
                __('post::post.messages.post_not_found'),
                [],
                404
            );
        }

        return DB::transaction(function () use ($request, $post) {

            $data = $request->validated();

            // Set same content for both Arabic and English in title and description
            if (isset($data['title'])) {
                $data['title'] = ['en' => $data['title'], 'ar' => $data['title']];
            }
            if (isset($data['description'])) {
                $data['description'] = ['en' => $data['description'], 'ar' => $data['description']];
            }

            // Handle Cover Image
            // if ($request->hasFile('cover_image')) {
            //     $data['cover_image'] = $request->file('cover_image')->store('posts/covers', 'public');
            // }

            // Update Post
            $post->update($data);

            // Handle Skills
            // if (isset($data['skills'])) {
            //     $skillIds = [];
            //     foreach ($data['skills'] as $skillName) {
            //         $skill = Skill::firstOrCreate(
            //             ['slug' => str()->slug($skillName)],
            //             ['name' => ['en' => $skillName, 'ar' => $skillName]]
            //         );
            //         $skillIds[] = $skill->id;
            //     }
            //     $post->skills()->sync($skillIds);
            // }

            // Handle Attachments (Append new ones)
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('posts/attachments', 'public');
                    $post->attachments()->create([
                        'file_path' => $path,
                        'file_type' => $file->getClientOriginalExtension(),
                    ]);
                }
            }

            return $this->successResponse(
                data: ['post' => (new PostDetailsResource($post))->resolve()],
                message: __('post::post.messages.updated')
            );
        });
    }


    
    public function destroy(Request $request,  $uuid): JsonResponse
    {
        // Use policy for authorization
        // Gate::authorize('delete', $post);

        $post = Post::where('uuid',$uuid)->where('user_id', $request->user()->id)->first();
        
        if($post == null) {
            return $this->errorResponse(
                __('post::post.messages.post_not_found'),
                [],
                404
            );
        }

        $post->delete();

        return $this->successResponse(message: __('post::post.messages.deleted'));
    }

    public function stop(Request $request, $uuid): JsonResponse
    {
        // Gate::authorize('update', $post);
        $post = Post::where('uuid',$uuid)->where('user_id', $request->user()->id)->first();
        
        if($post == null) {
            return $this->errorResponse(
                __('post::post.messages.post_not_found'),
                [],
                404
            );
        }

        if ($post->status !== 'active' && $post->status !== 'approved') {
            return $this->errorResponse(
                __('post::post.messages.cannot_stop_non_active'),
                [],
                400
            );
        }

        $post->update(['status' => 'inactive']);

        return $this->successResponse(
            data: ['post' => (new MyPostSummaryResource($post))->resolve()],
            message: __('post::post.messages.stopped')
        );
    }

    public function resume(Request $request, $uuid): JsonResponse
    {
        // Gate::authorize('update', $post);
        $post = Post::where('uuid',$uuid)->where('user_id', $request->user()->id)->first();
        
        if($post == null) {
            return $this->errorResponse(
                __('post::post.messages.post_not_found'),
                [],
                404
            );
        }

        if ($post->status !== 'inactive') {
            return $this->errorResponse(
                __('post::post.messages.cannot_resume_non_inactive'),
                [],
                400
            );
        }

        // Optional: Check if package is still valid/not expired before resuming
        if ($post->end_date && now()->gt($post->end_date)) {
             return $this->errorResponse(
                __('post::post.messages.package_expired'),
                []
                ,
                400
            );
        }

        $post->update(['status' => 'active']);

        return $this->successResponse(
            data: ['post' => (new MyPostSummaryResource($post))->resolve()],
            message: __('post::post.messages.resumed')
        );
    }
}
