<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Category\Models\Category;
use Modules\Product\Http\Requests\Api\ListProductRequest;
use Modules\Product\Http\Resources\ProductResource;
use Modules\Product\Models\Product;

class ProductController extends Controller
{
    use ApiResponse;

    public function index(ListProductRequest $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 10);
        $limit = max(1, min($limit, 50));

        $page = (int) ($request->input('page') ?? $request->input('pagination', 1));
        $page = max(1, $page);

        $status = strtolower($request->input('status', 'active'));

        $featured = $request->has('featured')
            ? filter_var($request->input('featured'), FILTER_VALIDATE_BOOL)
            : null;

        $newArrival = $request->has('new_arrival')
            ? filter_var($request->input('new_arrival'), FILTER_VALIDATE_BOOL)
            : null;

        $trending = $request->has('trending')
            ? filter_var($request->input('trending'), FILTER_VALIDATE_BOOL)
            : null;

        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $sort = $request->input('sort');

        $query = Product::query()
            ->with(['category', 'brand', 'galleries', 'tags'])
            ->when($status !== 'all', function ($q) use ($status) {
                $q->where('status', $status);
            })
            // Single category filter (includes children)
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $categoryId = $request->integer('category_id');
                // Get all descendant category IDs (including nested children)
                $categoryIds = Category::getDescendantIdsFor($categoryId);
                $q->whereIn('category_id', $categoryIds);
            })
            // Multiple categories filter (includes children for each)
            ->when($request->filled('category_ids'), function ($q) use ($request) {
                $categoryIds = $request->input('category_ids', []);
                $allCategoryIds = [];
                foreach ($categoryIds as $categoryId) {
                    $descendantIds = Category::getDescendantIdsFor((int) $categoryId);
                    $allCategoryIds = array_merge($allCategoryIds, $descendantIds);
                }
                $allCategoryIds = array_unique($allCategoryIds);
                $q->whereIn('category_id', $allCategoryIds);
            })
            // Single brand filter
            ->when($request->filled('brand_id'), fn ($q) => $q->where('brand_id', $request->integer('brand_id')))
            // Multiple brands filter
            ->when($request->filled('brand_ids'), fn ($q) => $q->whereIn('brand_id', $request->input('brand_ids', [])))
            // Multiple tags filter
            ->when($request->filled('tag_ids'), function ($q) use ($request) {
                $tagIds = $request->input('tag_ids', []);
                $q->whereHas('tags', fn ($tagQuery) => $tagQuery->whereIn('product_tags.id', $tagIds));
            })
            ->when(! is_null($featured), fn ($q) => $q->where('is_featured', $featured))
            ->when(! is_null($newArrival), fn ($q) => $q->where('is_new_arrival', $newArrival))
            ->when(! is_null($trending), fn ($q) => $q->where('is_trending', $trending))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->input('search').'%';
                $q->where(function ($subQuery) use ($term) {
                    $subQuery->where('title->en', 'like', $term)
                        ->orWhere('title->ar', 'like', $term)
                        ->orWhere('sku', 'like', $term);
                });
            })
            ->when($minPrice !== null, fn ($q) => $q->where('price', '>=', (float) $minPrice))
            ->when($maxPrice !== null, fn ($q) => $q->where('price', '<=', (float) $maxPrice))
            ->whereNull('deleted_at');

        // Apply sorting
        match ($sort) {
            'new_arrival' => $query->orderByDesc('is_new_arrival')
                ->orderByDesc('created_at')
                ->orderBy('position'),
            'trending' => $query->orderByDesc('is_trending')
                ->orderByDesc('created_at')
                ->orderBy('position'),
            'price_low_to_high' => $query->orderBy('price')
                ->orderBy('position')
                ->orderByDesc('created_at'),
            'price_high_to_low' => $query->orderByDesc('price')
                ->orderBy('position')
                ->orderByDesc('created_at'),
            'with_offers' => $query->where(function ($q) {
                $q->whereNotNull('offer_type')
                    ->whereNotNull('offer_price')
                    ->where(function ($subQ) {
                        $subQ->whereNull('offer_starts_at')
                            ->orWhere('offer_starts_at', '<=', now());
                    })
                    ->where(function ($subQ) {
                        $subQ->whereNull('offer_ends_at')
                            ->orWhere('offer_ends_at', '>=', now());
                    });
            })
                ->orderByDesc('offer_price')
                ->orderBy('position')
                ->orderByDesc('created_at'),
            default => $query->orderBy('position')
                ->orderByDesc('created_at'),
        };

        $paginator = $query->paginate($limit, ['*'], 'page', $page);

        return $this->successResponse(
            data: [
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                ],
                'products' => ProductResource::collection($paginator->getCollection())->resolve(),
            ],
            message: __('product::product.messages.listed'),
            request: $request
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $product = Product::withTrashed()->find($id);

        if (! $product) {
            return $this->errorResponse(
                message: __('product::product.messages.not_found'),
                status: 404,
                request: $request
            );
        }

        if ($product->status !== 'active' || $product->trashed()) {
            return $this->errorResponse(
                message: __('product::product.messages.not_available'),
                status: 404,
                request: $request
            );
        }

        $product->load(['category', 'brand', 'galleries', 'tags']);

        return $this->successResponse(
            data: ['product' => (new ProductResource($product))->resolve()],
            message: __('product::product.messages.loaded'),
            request: $request
        );
    }
}
