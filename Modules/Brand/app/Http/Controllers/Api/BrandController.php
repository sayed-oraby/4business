<?php

namespace Modules\Brand\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Brand\Http\Requests\Api\ListBrandRequest;
use Modules\Brand\Http\Resources\BrandResource;
use Modules\Brand\Models\Brand;

class BrandController extends Controller
{
    use ApiResponse;

    public function index(ListBrandRequest $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 10);
        $limit = max(1, min($limit, 50));
        $pageNumber = (int) ($request->input('page') ?? $request->input('pagination', 1));
        $pageNumber = max(1, $pageNumber);
        $status = strtolower($request->input('status', 'active'));
        $search = $request->input('search');

        $query = Brand::query()
            ->when($status !== 'all', fn ($q) => $q->whereRaw('LOWER(status) = ?', [$status])->whereNull('deleted_at'))
            ->when($search, function ($q) use ($search) {
                $term = '%'.$search.'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('title->en', 'like', $term)
                        ->orWhere('title->ar', 'like', $term);
                });
            })
            ->orderBy('position')
            ->orderBy('title->'.app()->getLocale());

        $paginator = $query->paginate($limit, ['*'], 'page', $pageNumber);

        return $this->successResponse(
            data: [
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                ],
                'brands' => BrandResource::collection($paginator->getCollection())->resolve(),
            ],
            message: __('brand::brand.messages.listed'),
            request: $request
        );
    }
}
