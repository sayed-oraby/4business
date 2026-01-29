<?php

namespace Modules\Category\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Category\Http\Requests\Dashboard\StoreCategoryRequest;
use Modules\Category\Http\Requests\Dashboard\UpdateCategoryRequest;
use Modules\Category\Http\Resources\CategoryResource;
use Modules\Category\Models\Category;
use Modules\Category\Repositories\CategoryRepository;
use Modules\Category\Services\CategoryService;

class CategoryController extends Controller
{
    use ApiResponse;
    use AuthorizesRequests;

    public function __construct(
        protected CategoryRepository $repository,
        protected CategoryService $service) {}

    public function index()
    {
        $this->authorize('viewAny', Category::class);

        return view('category::dashboard.index', [
            'statuses' => Category::STATUSES,
            'stats' => [
                'active' => Category::where('status', 'active')->count(),
                'draft' => Category::where('status', 'draft')->count(),
                'archived' => Category::onlyTrashed()->count(),
            ],
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Category::class);

        $state = $request->string('state')->toString();
        $trashed = match ($state) {
            'archived' => 'only',
            'all' => 'with',
            default => $request->string('trashed')->toString(),
        };

        $ordering = [
            1 => 'title->'.app()->getLocale(),
            2 => 'status',
            3 => 'parent_id',
            4 => 'is_featured',
            5 => 'updated_at',
        ];

        $query = $this->repository->datatable([
            'status' => $request->string('status')->toString(),
            'search' => $request->input('search.value', $request->input('search')),
            'trashed' => $trashed,
            'featured' => $request->has('featured') && $request->input('featured') !== ''
                ? (bool) $request->input('featured')
                : null,
            'parent_id' => $request->filled('parent_id') ? $request->integer('parent_id') : null,
        ]);

        if ($request->filled('order.0.column')) {
            $column = (int) $request->input('order.0.column');
            $direction = $request->input('order.0.dir', 'desc');
            $query->orderBy($ordering[$column] ?? 'position', $direction);
        } else {
            $query->orderBy('position')->orderByDesc('created_at');
        }

        $recordsTotal = Category::withTrashed()->count();
        $recordsFiltered = (clone $query)->count();

        $length = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $draw = (int) $request->input('draw', 1);

        $categories = (clone $query)
            ->skip($start)
            ->take($length)
            ->with('parent')
            ->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => CategoryResource::collection($categories)->resolve(),
        ]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->service->store($request->validated(), $request->user());

        return $this->successResponse(
            data: ['category' => (new CategoryResource($category))->resolve()],
            message: __('category::category.messages.created'),
            status: 201,
        );
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category = $this->service->update($category, $request->validated(), $request->user());

        return $this->successResponse(
            data: ['category' => (new CategoryResource($category))->resolve()],
            message: __('category::category.messages.updated'),
        );
    }

    public function destroy(Category $category, Request $request): JsonResponse
    {
        $this->authorize('delete', $category);
        $this->service->delete($category, $request->user());

        return $this->successResponse(
            data: null,
            message: __('category::category.messages.deleted'),
        );
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        abort_unless($request->user()?->can('categories.delete'), 403);

        $ids = collect($request->input('ids', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->all();

        if (! empty($ids)) {
            $this->service->bulkDelete($ids, $request->user());
        }

        return $this->successResponse(
            data: null,
            message: __('category::category.messages.bulk_deleted'),
        );
    }

    public function parents(Request $request): JsonResponse
    {
        abort_unless($request->user()?->can('categories.view'), 403);

        $exclude = $request->integer('exclude');
        $search = $request->string('search')->toString();

        $query = Category::query()
            ->parents()
            ->whereNull('deleted_at');

        if ($exclude) {
            $query->where('id', '!=', $exclude);
        }

        if ($search) {
            $term = '%'.$search.'%';
            $query->where(function ($q) use ($term) {
                $q->where('title->en', 'like', $term)
                    ->orWhere('title->ar', 'like', $term);
            });
        }

        $categories = $query->orderBy('title->'.app()->getLocale())->limit(25)->get();

        return response()->json([
            'results' => $categories->map(fn ($cat) => [
                'id' => $cat->id,
                'text' => $cat->title,
            ]),
        ]);
    }
}
