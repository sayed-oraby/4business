<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Http\Resources\ProductTagResource;
use Modules\Product\Models\ProductTag;

class ProductTagController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 50);
        $limit = max(1, min($limit, 100));

        $search = $request->input('search');

        $query = ProductTag::query()
            ->when($search, function ($q) use ($search) {
                $term = '%'.$search.'%';
                $q->where(function ($subQuery) use ($term) {
                    $subQuery->where('title->en', 'like', $term)
                        ->orWhere('title->ar', 'like', $term)
                        ->orWhere('slug', 'like', $term);
                });
            })
            ->orderBy('title->'.app()->getLocale())
            ->limit($limit);

        $tags = $query->get();

        return $this->successResponse(
            data: ['tags' => ProductTagResource::collection($tags)->resolve()],
            message: __('product::product.messages.tags_listed'),
            request: $request
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $tag = ProductTag::find($id);

        if (! $tag) {
            return $this->errorResponse(
                message: __('product::product.messages.tag_not_found'),
                status: 404,
                request: $request
            );
        }

        return $this->successResponse(
            data: ['tag' => (new ProductTagResource($tag))->resolve()],
            message: __('product::product.messages.tag_loaded'),
            request: $request
        );
    }
}

