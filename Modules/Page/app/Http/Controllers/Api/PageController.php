<?php

namespace Modules\Page\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Page\Http\Requests\Api\ListPageRequest;
use Modules\Page\Http\Resources\PageResource;
use Modules\Page\Models\Page;

class PageController extends Controller
{
    use ApiResponse;

    public function index(ListPageRequest $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 10);
        $limit = max(1, min($limit, 50));
        $pageNumber = (int) ($request->input('page') ?? $request->input('pagination', 1));
        $pageNumber = max(1, $pageNumber);
        $status = $request->input('status', 'published');

        $query = Page::query()
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->input('search').'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('title->en', 'like', $term)
                        ->orWhere('title->ar', 'like', $term)
                        ->orWhere('description->en', 'like', $term)
                        ->orWhere('description->ar', 'like', $term)
                        ->orWhere('slug', 'like', $term);
                });
            })
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
                'pages' => PageResource::collection($paginator->getCollection())->resolve(),
            ],
            message: __('page::page.messages.listed'),
        );
    }
}
