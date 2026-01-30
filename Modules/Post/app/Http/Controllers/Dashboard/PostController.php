<?php

namespace Modules\Post\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Post\Models\Post;

class PostController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $query = Post::with(['user', 'postType', 'package', 'attachments']);

            // Search
            if ($search = $request->input('search.value')) {
                $query->where(function ($q) use ($search) {
                    $q->where('title->en', 'like', "%{$search}%")
                        ->orWhere('title->ar', 'like', "%{$search}%");
                });
            }

            // Filters
            if ($status = $request->input('status')) {
                $query->where('status', $status);
            }
            if ($categoryId = $request->input('category_id')) {
                $query->where('category_id', $categoryId);
            }
            if ($postTypeId = $request->input('post_type_id')) {
                $query->where('post_type_id', $postTypeId);
            }
            if ($packageId = $request->input('package_id')) {
                $query->where('package_id', $packageId);
            }
            if ($request->filled('is_paid')) {
                $query->where('is_paid', $request->input('is_paid') === '1');
            }
            if ($gender = $request->input('gender')) {
                $query->where('gender', $gender);
            }
            if ($cityId = $request->input('city_id')) {
                $query->where('city_id', $cityId);
            }
            if ($dateFrom = $request->input('date_from')) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo = $request->input('date_to')) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            // Pagination
            $totalRecords = Post::count();
            $filteredRecords = $query->count();

            $posts = $query->skip($request->input('start', 0))
                ->take($request->input('length', 10))
                ->latest()
                ->get();

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $posts,
            ]);
        }

        // Statistics for overview cards (matching dashboard home logic)
        $stats = [
            'total' => Post::count(),
            // Active: approved, not expired, and properly paid if paid package
            'active' => Post::where('status', 'approved')
                ->where(function ($query) {
                    $query->where('end_date', '>=', now())
                        ->orWhereNull('end_date');
                })
                ->where(function ($query) {
                    // Free packages
                    $query->whereDoesntHave('package')
                        ->orWhereHas('package', function ($q) {
                            $q->where('price', '<=', 0);
                        })
                        // OR paid packages that have been paid
                        ->orWhere('is_paid', true);
                })
                ->count(),
            // Pending: only posts ready for review (free OR paid)
            'pending' => Post::where('status', 'pending')
                ->where(function ($query) {
                    $query->whereDoesntHave('package')
                        ->orWhereHas('package', function ($q) {
                            $q->where('price', '<=', 0);
                        })
                        ->orWhere('is_paid', true);
                })
                ->count(),
            'expired' => Post::where('status', 'expired')
                ->orWhere(function ($query) {
                    $query->where('end_date', '<', now())
                        ->whereNotNull('end_date');
                })
                ->count(),
            // Featured: package price > 0 AND is_paid = true
            'featured' => Post::where('is_paid', true)
                ->whereHas('package', function ($q) {
                    $q->where('price', '>', 0);
                })
                ->count(),
        ];

        // Get filter options
        $categories = \Modules\Category\Models\Category::whereNull('parent_id')->get();
        $postTypes = \Modules\Post\Models\PostType::all();
        $packages = \Modules\Post\Models\Package::all();
        $cities = \Modules\Shipping\Models\ShippingCity::all();

        return view('post::dashboard.posts.index', compact('stats', 'categories', 'postTypes', 'packages', 'cities'));
    }

    public function show(Post $post)
    {
        $post->load(['user', 'category', 'postType', 'package', 'city', 'skills', 'attachments', 'jobOffers.user']);

        return view('post::dashboard.posts.show', compact('post'));
    }

    public function updateStatus(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|string|nullable',
        ]);

        $post->update([
            'status' => $validated['status'],
            'rejection_reason' => $validated['status'] === 'rejected' ? $validated['rejection_reason'] : null,
        ]);

        // TODO: Send notification to user about status change

        return $this->successResponse(
            data: ['post' => $post],
            message: 'Post status updated successfully.'
        );
    }

    public function edit(Post $post)
    {
        $post->load(['skills', 'attachments']);
        $categories = \Modules\Category\Models\Category::where('status', 'active')->get();
        $types = \Modules\Post\Models\PostType::where('status', true)->get();
        $packages = \Modules\Post\Models\Package::where('status', true)->get();
        $cities = \Modules\Shipping\Models\ShippingCity::all();

        $states = \Modules\Shipping\Models\ShippingState::all();

        $skills = \Modules\Post\Models\Skill::all();

        return view('post::dashboard.posts.edit', compact('post', 'categories', 'types', 'packages', 'cities', 'states', 'skills'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title.en' => 'required|string',
            'title.ar' => 'required|string',
            'description.en' => 'required|string',
            'description.ar' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'post_type_id' => 'required|exists:post_types,id',
            'package_id' => 'required|exists:packages,id',
            // 'city_id' => 'nullable|exists:shipping_cities,id',
            // 'years_of_experience' => 'nullable|integer|min:0',
            'state_id' => 'nullable|exists:shipping_states,id',
            'price' => 'nullable|integer|min:0',
            // 'skills' => 'nullable|array',
            // 'skills.*' => 'exists:skills,id',
            'status' => 'required|in:pending,approved,rejected,expired',
            'cover_image' => 'nullable|image',

            // New Fields
            // 'full_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            // 'gender' => 'required|in:male,female,both',
            // 'birthdate' => 'nullable|date',
            'display_personal_details' => 'boolean',
            'is_paid' => 'boolean',
            'is_price_contact' => 'boolean',
            'whatsapp_number' => 'nullable|string|max:20',

            // Attachments
            'attachments' => 'nullable|array',
            'attachments.*.file' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:25600',
            'attachments.*.attachment_title' => 'nullable|string|max:255',
            'deleted_attachments' => 'nullable|array',
            'deleted_attachments.*' => 'exists:post_attachments,id',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('posts/covers', 'public');
        }

        // Handle Boolean Fields
        $validated['display_personal_details'] = $request->has('display_personal_details');
        $validated['is_paid'] = $request->has('is_paid');
        $validated['is_price_contact'] = $request->has('is_price_contact');

        $post->update($validated);

        // if (isset($validated['skills'])) {
        //     $post->skills()->sync($validated['skills']);
        // }

        // Handle Attachments
        if ($request->has('attachments')) {
            $attachments = $request->input('attachments');
            $files = $request->file('attachments');

            // Handle API format (key as title) or Form Repeater format (array of objects)
            if (is_array($files)) {
                foreach ($files as $key => $file) {
                    // Check if it's the repeater format: attachments[0][file]
                    if (is_array($file) && isset($file['file'])) {
                        $uploadedFile = $file['file'];
                        $title = $attachments[$key]['attachment_title'] ?? $attachments[$key]['title'] ?? $uploadedFile->getClientOriginalName();

                        $path = $uploadedFile->store('posts/attachments', 'public');
                        $post->attachments()->create([
                            'title' => $title,
                            'file_path' => $path,
                            'file_type' => $uploadedFile->getClientOriginalExtension(),
                        ]);
                    }
                    // Check if it's the API format: attachments['My Title'] = file
                    elseif ($file instanceof \Illuminate\Http\UploadedFile) {
                        $path = $file->store('posts/attachments', 'public');
                        $post->attachments()->create([
                            'title' => is_string($key) ? $key : $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_type' => $file->getClientOriginalExtension(),
                        ]);
                    }
                }
            }
        }

        // Handle Deleted Attachments
        if ($request->filled('deleted_attachments')) {
            $post->attachments()->whereIn('id', $request->input('deleted_attachments'))->delete();
        }

        return redirect()->route('dashboard.posts.show', $post->id)->with('success', __('post::post.messages.updated'));
    }

    public function deleteAttachment(Post $post, $attachmentId): JsonResponse
    {
        $attachment = $post->attachments()->findOrFail($attachmentId);

        // Delete file from storage
        if ($attachment->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return $this->successResponse(message: 'Attachment deleted successfully.');
    }

    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return $this->successResponse(message: 'Post deleted successfully.');
    }
}
