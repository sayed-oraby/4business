<?php

namespace Modules\Blog\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\Http\Requests\Api\ListBlogRequest;
use Modules\Blog\Http\Resources\BlogResource;
use Modules\Blog\Models\Blog;

class BlogController extends Controller
{
    use ApiResponse;

    public function index(ListBlogRequest $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 10);
        $limit = max(1, min($limit, 50));
        $pageNumber = (int) ($request->input('page') ?? $request->input('pagination', 1));
        $pageNumber = max(1, $pageNumber);
        $status = $request->input('status', 'published');
        $tagId = $request->integer('tag_id');

        $query = Blog::query()
            ->with(['tags', 'galleries'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status)->whereNull('deleted_at'))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->input('search').'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('title->en', 'like', $term)
                        ->orWhere('title->ar', 'like', $term)
                        ->orWhere('short_description->en', 'like', $term)
                        ->orWhere('short_description->ar', 'like', $term);
                });
            })
            ->when($tagId, fn ($q) => $q->whereHas('tags', fn ($sub) => $sub->where('blog_tags.id', $tagId)))
            ->orderByDesc('created_at');

        $paginator = $query->paginate($limit, ['*'], 'page', $pageNumber);

        return $this->successResponse(
            data: [
                'status' => $status === 'all' ? null : $status,
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                ],
                'blogs' => BlogResource::collection($paginator->getCollection())->resolve(),
            ],
            message: __('blog::blog.messages.listed'),
            request: $request
        );
    }

    public function show(Request $request, Blog $blog): JsonResponse
    {
        if ($blog->status !== 'published' || $blog->trashed()) {
            return $this->errorResponse(
                message: __('blog::blog.messages.not_available'),
                status: 404,
                request: $request
            );
        }

        $blog->load(['tags', 'galleries', 'creator']);

        return $this->successResponse(
            data: ['blog' => (new BlogResource($blog))->resolve()],
            message: __('blog::blog.messages.loaded'),
            request: $request
        );
    }
}
