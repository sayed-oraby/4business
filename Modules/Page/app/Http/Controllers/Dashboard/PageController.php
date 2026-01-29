<?php

namespace Modules\Page\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Page\Http\Requests\Dashboard\StorePageRequest;
use Modules\Page\Http\Requests\Dashboard\UpdatePageRequest;
use Modules\Page\Http\Resources\PageResource;
use Modules\Page\Models\Page;
use Modules\Page\Repositories\PageRepository;
use Modules\Page\Services\PageService;

class PageController extends Controller
{
    use AuthorizesRequests;
    use ApiResponse;

    public function __construct(
        protected PageRepository $repository,
        protected PageService $service
    ) {
    }

    public function index()
    {
        $this->authorize('viewAny', Page::class);

        return view('page::dashboard.index', [
            'statuses' => Page::STATUSES,
            'states' => ['active', 'archived', 'all'],
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Page::class);

        $state = $request->string('state')->toString();
        $trashed = match ($state) {
            'archived' => 'only',
            'all' => 'with',
            default => $request->string('trashed')->toString(),
        };

        $ordering = [
            1 => 'title->'.app()->getLocale(),
            2 => 'slug',
            3 => 'status',
            4 => 'updated_at',
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

        $recordsTotal = Page::withTrashed()->count();
        $recordsFiltered = (clone $query)->count();

        $length = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $draw = (int) $request->input('draw', 1);

        $pages = (clone $query)->skip($start)->take($length)->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => PageResource::collection($pages)->resolve(),
        ]);
    }

    public function store(StorePageRequest $request): JsonResponse
    {
        $page = $this->service->store($request->validated(), $request->user());

        return $this->successResponse(
            data: ['page' => (new PageResource($page))->resolve()],
            message: __('page::page.messages.created'),
            status: 201,
            request: $request
        );
    }

    public function update(UpdatePageRequest $request, Page $page): JsonResponse
    {
        $page = $this->service->update($page, $request->validated(), $request->user());

        return $this->successResponse(
            data: ['page' => (new PageResource($page))->resolve()],
            message: __('page::page.messages.updated'),
            request: $request
        );
    }

    public function destroy(Page $page, Request $request): JsonResponse
    {
        $this->authorize('delete', $page);

        $this->service->delete($page, $request->user());

        return $this->successResponse(
            data: null,
            message: __('page::page.messages.deleted'),
            request: $request
        );
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        abort_unless($request->user()?->can('pages.delete'), 403);

        $ids = collect($request->input('ids', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->all();

        if (! empty($ids)) {
            $this->service->bulkDelete($ids, $request->user());
        }

        return $this->successResponse(
            data: null,
            message: __('page::page.messages.bulk_deleted'),
            request: $request
        );
    }
}
