<?php

namespace Modules\Blog\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\Http\Requests\Dashboard\StoreTagRequest;
use Modules\Blog\Http\Resources\BlogTagResource;
use Modules\Blog\Models\BlogTag;
use Modules\Blog\Services\BlogService;

class BlogTagController extends Controller
{
    use AuthorizesRequests;
    use ApiResponse;

    public function __construct(
        protected BlogService $service) {
    }

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()?->can('blogs.view'), 403);

        $search = $request->input('search');

        $tags = BlogTag::query()
            ->when($search, function ($q) use ($search) {
                $term = '%'.$search.'%';
                $q->where('title->en', 'like', $term)
                    ->orWhere('title->ar', 'like', $term);
            })
            ->orderBy('title->'.app()->getLocale())
            ->limit(50)
            ->get();

        return response()->json([
            'tags' => BlogTagResource::collection($tags)->resolve(),
        ]);
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = $this->service->createTag($request->validated()['title']);

        return $this->successResponse(
            data: ['tag' => (new BlogTagResource($tag))->resolve()],
            message: __('blog::blog.messages.tag_created'),
            status: 201,
            request: $request
        );
    }
}
