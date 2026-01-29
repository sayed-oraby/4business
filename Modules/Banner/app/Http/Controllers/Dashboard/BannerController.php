<?php

namespace Modules\Banner\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Banner\Http\Requests\Dashboard\StoreBannerRequest;
use Modules\Banner\Http\Requests\Dashboard\UpdateBannerRequest;
use Modules\Banner\Http\Resources\BannerResource;
use Modules\Banner\Models\Banner;
use Modules\Banner\Repositories\BannerRepository;
use Modules\Banner\Services\BannerService;

class BannerController extends Controller
{
    use AuthorizesRequests;
    use ApiResponse;

    public function __construct(
        protected BannerRepository $repository,
        protected BannerService $service
    ) {
    }

    public function index()
    {
        $this->authorize('viewAny', Banner::class);

        return view('banner::dashboard.index', [
            'placements' => config('banner.placements'),
            'statuses' => Banner::STATUSES,
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Banner::class);

        $columns = [
            0 => 'sort_order',
            1 => 'title->'.app()->getLocale(),
            2 => 'placement',
            3 => 'status',
            4 => 'starts_at',
        ];

        $trashed = $request->string('trashed')->toString();

        $query = $this->repository->datatable([
            'placement' => $request->string('placement')->toString(),
            'status' => $request->string('status')->toString(),
            'search' => $request->input('search.value', $request->input('search')),
            'trashed' => $trashed,
        ]);

        if ($request->filled('order.0.column')) {
            $columnIndex = (int) $request->input('order.0.column');
            $direction = $request->input('order.0.dir', 'desc');
            $column = $columns[$columnIndex] ?? 'created_at';
            $query->orderBy($column, $direction);
        } else {
            $query->orderByDesc('created_at');
        }

        $recordsTotal = Banner::count();
        $recordsFiltered = (clone $query)->count();

        $length = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $draw = (int) $request->input('draw', 1);

        $banners = (clone $query)->skip($start)->take($length)->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => BannerResource::collection($banners)->resolve(),
        ]);
    }

    public function store(StoreBannerRequest $request): JsonResponse
    {
        $banner = $this->service->store($request->validated(), $request->user());

        return $this->successResponse(
            data: ['banner' => (new BannerResource($banner))->resolve()],
            message: __('banner::banner.messages.created'),
            status: 201
        );
    }

    public function update(UpdateBannerRequest $request, Banner $banner): JsonResponse
    {
        $banner = $this->service->update($banner, $request->validated(), auth()->user());

        return $this->successResponse(
            data: ['banner' => (new BannerResource($banner))->resolve()],
            message: __('banner::banner.messages.updated')
        );
    }

    public function destroy(Banner $banner): JsonResponse
    {
        $this->authorize('delete', $banner);

        $this->service->delete($banner, auth()->user());

        return $this->successResponse(
            data: null,
            message: __('banner::banner.messages.deleted')
        );
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        abort_unless($request->user()?->can('banners.delete'), 403);

        $ids = collect($request->input('ids', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->all();

        if (! empty($ids)) {
            $this->service->bulkDelete($ids, $request->user());
        }

        return $this->successResponse(
            data: null,
            message: __('banner::banner.messages.bulk_deleted')
        );
    }
}
