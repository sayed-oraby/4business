<?php

namespace Modules\Brand\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Brand\Http\Requests\Dashboard\StoreBrandRequest;
use Modules\Brand\Http\Requests\Dashboard\UpdateBrandRequest;
use Modules\Brand\Http\Resources\BrandResource;
use Modules\Brand\Models\Brand;
use Modules\Brand\Repositories\BrandRepository;
use Modules\Brand\Services\BrandService;

class BrandController extends Controller
{
    use AuthorizesRequests;
    use ApiResponse;

    public function __construct(
        protected BrandRepository $repository,
        protected BrandService $service) {
    }

    public function index()
    {
        $this->authorize('viewAny', Brand::class);

        return view('brand::dashboard.index', [
            'statuses' => Brand::STATUSES,
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Brand::class);

        $state = $request->string('state')->toString();
        $trashed = match ($state) {
            'archived' => 'only',
            'all' => 'with',
            default => $request->string('trashed')->toString(),
        };

        $ordering = [
            1 => 'title->'.app()->getLocale(),
            2 => 'status',
            3 => 'position',
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
            $query->orderBy($ordering[$column] ?? 'position', $direction);
        } else {
            $query->orderBy('position')->orderByDesc('created_at');
        }

        $recordsTotal = Brand::withTrashed()->count();
        $recordsFiltered = (clone $query)->count();

        $length = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $draw = (int) $request->input('draw', 1);

        $brands = (clone $query)->skip($start)->take($length)->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => BrandResource::collection($brands)->resolve(),
        ]);
    }

    public function store(StoreBrandRequest $request): JsonResponse
    {
        $brand = $this->service->store($request->validated(), $request->user());

        return $this->successResponse(
            data: ['brand' => (new BrandResource($brand))->resolve()],
            message: __('brand::brand.messages.created'),
            status: 201,
            request: $request
        );
    }

    public function update(UpdateBrandRequest $request, Brand $brand): JsonResponse
    {
        $brand = $this->service->update($brand, $request->validated(), $request->user());

        return $this->successResponse(
            data: ['brand' => (new BrandResource($brand))->resolve()],
            message: __('brand::brand.messages.updated'),
            request: $request
        );
    }

    public function destroy(Brand $brand, Request $request): JsonResponse
    {
        $this->authorize('delete', $brand);
        $this->service->delete($brand, $request->user());

        return $this->successResponse(
            data: null,
            message: __('brand::brand.messages.deleted'),
            request: $request
        );
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        abort_unless($request->user()?->can('brands.delete'), 403);

        $ids = collect($request->input('ids', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->all();

        if (! empty($ids)) {
            $this->service->bulkDelete($ids, $request->user());
        }

        return $this->successResponse(
            data: null,
            message: __('brand::brand.messages.bulk_deleted'),
            request: $request
        );
    }
}
