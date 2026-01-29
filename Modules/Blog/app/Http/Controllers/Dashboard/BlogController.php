<?php

namespace Modules\Blog\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\Http\Requests\Dashboard\StoreBlogRequest;
use Modules\Blog\Http\Requests\Dashboard\UpdateBlogRequest;
use Modules\Blog\Http\Resources\BlogGalleryResource;
use Modules\Blog\Http\Resources\BlogResource;
use Modules\Blog\Http\Resources\BlogTagResource;
use Modules\Blog\Models\Blog;
use Modules\Blog\Models\BlogTag;
use Modules\Blog\Repositories\BlogRepository;
use Modules\Blog\Services\BlogService;

class BlogController extends Controller
{
    use AuthorizesRequests;
    use ApiResponse;

    public function __construct(
        protected BlogRepository $repository,
        protected BlogService $service) {
    }

    public function index()
    {
        $this->authorize('viewAny', Blog::class);

        $tags = BlogTag::orderBy('title->'.app()->getLocale())->get();

        return view('blog::dashboard.index', [
            'statuses' => Blog::STATUSES,
            'tags' => BlogTagResource::collection($tags)->resolve(),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Blog::class);

        $state = $request->string('state')->toString();
        $trashed = match ($state) {
            'archived' => 'only',
            'all' => 'with',
            default => $request->string('trashed')->toString(),
        };

        $ordering = [
            1 => 'title->'.app()->getLocale(),
            2 => 'status',
            3 => 'updated_at',
        ];

        $query = $this->repository->datatable([
            'status' => $request->string('status')->toString(),
            'search' => $request->input('search.value', $request->input('search')),
            'trashed' => $trashed,
        ]);

        if ($request->filled('order.0.column')) {
            $column = (int) $request->input('order.0.column');
            $direction = $request->input('order.0.dir', 'desc');
            $query->orderBy($ordering[$column] ?? 'created_at', $direction);
        } else {
            $query->orderByDesc('created_at');
        }

        $recordsTotal = Blog::withTrashed()->count();
        $recordsFiltered = (clone $query)->count();

        $length = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $draw = (int) $request->input('draw', 1);

        $blogs = (clone $query)
            ->skip($start)
            ->take($length)
            ->with(['creator', 'tags', 'galleries'])
            ->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => BlogResource::collection($blogs)->resolve(),
        ]);
    }

    public function store(StoreBlogRequest $request): JsonResponse
    {
        $blog = $this->service->store($request->validated(), $request->user());

        $blog->load(['creator', 'tags', 'galleries']);

        return $this->successResponse(
            data: ['blog' => (new BlogResource($blog))->resolve()],
            message: __('blog::blog.messages.created'),
            status: 201,
            request: $request
        );
    }

    public function update(UpdateBlogRequest $request, Blog $blog): JsonResponse
    {
        $blog = $this->service->update($blog, $request->validated(), $request->user());
        $blog->load(['creator', 'tags', 'galleries']);

        return $this->successResponse(
            data: ['blog' => (new BlogResource($blog))->resolve()],
            message: __('blog::blog.messages.updated'),
            request: $request
        );
    }

    public function destroy(Blog $blog, Request $request): JsonResponse
    {
        $this->authorize('delete', $blog);

        $this->service->delete($blog, $request->user());

        return $this->successResponse(
            data: null,
            message: __('blog::blog.messages.deleted'),
            request: $request
        );
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        abort_unless($request->user()?->can('blogs.delete'), 403);

        $ids = collect($request->input('ids', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->all();

        if (! empty($ids)) {
            $this->service->bulkDelete($ids, $request->user());
        }

        return $this->successResponse(
            data: null,
            message: __('blog::blog.messages.bulk_deleted'),
            request: $request
        );
    }

    public function gallery(Blog $blog): JsonResponse
    {
        $this->authorize('view', $blog);

        return response()->json([
            'gallery' => BlogGalleryResource::collection(
                $blog->galleries()->orderBy('sort_order')->orderByDesc('id')->get()
            )->resolve(),
        ]);
    }
}
