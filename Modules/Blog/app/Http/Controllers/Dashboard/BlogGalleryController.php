<?php

namespace Modules\Blog\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\Http\Resources\BlogGalleryResource;
use Modules\Blog\Models\Blog;
use Modules\Blog\Models\BlogGallery;
use Modules\Blog\Services\BlogService;

class BlogGalleryController extends Controller
{
    use AuthorizesRequests;
    use ApiResponse;

    public function __construct(
        protected BlogService $service) {
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'max:5120'],
            'upload_token' => ['required_without:blog_id', 'string', 'max:64'],
            'blog_id' => ['nullable', 'integer', 'exists:blogs,id'],
        ]);

        $blog = null;
        if ($request->filled('blog_id')) {
            $blog = Blog::findOrFail($request->input('blog_id'));
            $this->authorize('update', $blog);
        } else {
            abort_unless($request->user()?->can('blogs.create'), 403);
        }

        $gallery = $this->service->uploadGallery(
            blog: $blog,
            uploadToken: $request->input('upload_token', ''),
            file: $request->file('file'),
            actor: $request->user()
        );

        return response()->json([
            'gallery' => (new BlogGalleryResource($gallery))->resolve(),
        ], 201);
    }

    public function destroy(BlogGallery $gallery, Request $request): JsonResponse
    {
        if ($gallery->blog) {
            $this->authorize('update', $gallery->blog);
        } else {
            abort_unless($request->user()?->can('blogs.create'), 403);
        }

        $this->service->removeGallery($gallery);

        return $this->successResponse(
            data: null,
            message: __('blog::blog.messages.gallery_deleted'),
            request: $request
        );
    }
}
