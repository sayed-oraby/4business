<?php

namespace Modules\Category\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Category\Http\Requests\Api\ListCategoryRequest;
use Modules\Category\Http\Resources\CategoryResource;
use Modules\Category\Models\Category;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index(ListCategoryRequest $request): JsonResponse
    {
        // Get limit from request, default 10, max 50
        $limit = (int) $request->input('limit', 10);
        $limit = max(1, min($limit, 50));
        
        // Build query
        $query = Category::query()
            ->with('parent')
            ->where('status', 'active')
            ->whereNull('deleted_at');

        // Filter by parent_id
        // If parent_id is provided, return children of that parent
        // If parent_id is not provided, return only root categories (parent_id is null)
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        } else {
            // By default, return only root (parent) categories
            $query->whereNull('parent_id');
        }

        // Filter by featured status
        if ($request->filled('featured') && $request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Order by position and title
        $query->orderBy('position')
              ->orderBy('title->'.app()->getLocale());

        // Paginate
        $paginator = $query->paginate($limit);

        return $this->successResponse(
            data: [
                'categories' => CategoryResource::collection($paginator->items())->resolve(),
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                ],
            ],
            message: __('category::category.messages.listed')
        );
    }
}

