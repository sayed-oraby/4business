<?php

namespace Modules\Product\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Product\Http\Requests\Dashboard\StoreProductRequest;
use Modules\Product\Http\Requests\Dashboard\StoreTagRequest;
use Modules\Product\Http\Requests\Dashboard\UpdateProductRequest;
use Modules\Product\Http\Resources\ProductGalleryResource;
use Modules\Product\Http\Resources\ProductResource;
use Modules\Product\Http\Resources\ProductTagResource;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductGallery;
use Modules\Product\Models\ProductTag;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Services\ProductService;
use Modules\Category\Models\Category;
use Modules\Brand\Models\Brand;

class ProductController extends Controller
{
    use AuthorizesRequests;
    use ApiResponse;

    public function __construct(
        protected ProductRepository $repository,
        protected ProductService $service) {
    }

    public function index()
    {
        $this->authorize('viewAny', Product::class);

        return view('product::dashboard.index', [
            'statuses' => Product::STATUSES,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Product::class);

        $product = new Product(['status' => Product::STATUSES[0] ?? 'draft']);

        return view('product::dashboard.create', [
            'product' => $product,
            'productResource' => (new ProductResource($product))->resolve(),
            'statuses' => Product::STATUSES,
            'availableLocales' => available_locales(),
            'galleryToken' => Str::uuid()->toString(),
        ]);
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        $product->load(['category', 'brand', 'tags', 'galleries']);

        return view('product::dashboard.edit', [
            'product' => $product,
            'productResource' => (new ProductResource($product))->resolve(),
            'statuses' => Product::STATUSES,
            'availableLocales' => available_locales(),
            'galleryToken' => Str::uuid()->toString(),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $state = $request->string('state')->toString();
        $trashed = match ($state) {
            'archived' => 'only',
            'all' => 'with',
            default => $request->string('trashed')->toString(),
        };

        $ordering = [
            1 => 'title->'.app()->getLocale(),
            2 => 'sku',
            3 => 'price',
            4 => 'status',
            5 => 'qty',
            6 => 'updated_at',
        ];

        $query = $this->repository->datatable([
            'status' => $request->string('status')->toString(),
            'featured' => $request->has('featured') ? $request->boolean('featured') : null,
            'is_new_arrival' => $request->has('new_arrival') ? $request->boolean('new_arrival') : null,
            'is_trending' => $request->has('trending') ? $request->boolean('trending') : null,
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

        $recordsTotal = Product::withTrashed()->count();
        $recordsFiltered = (clone $query)->count();

        $length = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $draw = (int) $request->input('draw', 1);

        $products = (clone $query)
            ->skip($start)
            ->take($length)
            ->with(['category', 'brand'])
            ->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => ProductResource::collection($products)->resolve(),
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->service->store($request->validated(), $request->user());

        return $this->successResponse(
            data: ['product' => (new ProductResource($product->load(['category', 'brand'])))->resolve()],
            message: __('product::product.messages.created'),
            status: 201,
            request: $request
        );
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->service->update($product, $request->validated(), $request->user());

        return $this->successResponse(
            data: ['product' => (new ProductResource($product->load(['category', 'brand'])))->resolve()],
            message: __('product::product.messages.updated'),
            request: $request
        );
    }

    public function destroy(Product $product, Request $request): JsonResponse
    {
        $this->authorize('delete', $product);
        $this->service->delete($product, $request->user());

        return $this->successResponse(
            data: null,
            message: __('product::product.messages.deleted'),
            request: $request
        );
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        abort_unless($request->user()?->can('products.delete'), 403);

        $ids = collect($request->input('ids', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->all();

        if (! empty($ids)) {
            $this->service->bulkDelete($ids, $request->user());
        }

        return $this->successResponse(
            data: null,
            message: __('product::product.messages.bulk_deleted'),
            request: $request
        );
    }

    public function galleryUpload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'max:5120'],
            'upload_token' => ['required_without:product_id', 'string', 'max:64'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
        ]);

        $product = null;
        if ($request->filled('product_id')) {
            $product = Product::findOrFail($request->integer('product_id'));
            $this->authorize('update', $product);
        } else {
            abort_unless($request->user()?->can('products.create'), 403);
        }

        $gallery = $this->service->uploadGallery(
            product: $product,
            uploadToken: $request->input('upload_token', ''),
            file: $request->file('file'),
            actor: $request->user()
        );

        return response()->json([
            'gallery' => (new ProductGalleryResource($gallery))->resolve(),
        ], 201);
    }

    public function galleryDestroy(ProductGallery $gallery, Request $request): JsonResponse
    {
        if ($gallery->product) {
            $this->authorize('update', $gallery->product);
        } else {
            abort_unless($request->user()?->can('products.create'), 403);
        }

        $this->service->removeGallery($gallery);

        return $this->successResponse(
            data: null,
            message: __('product::product.messages.gallery_deleted'),
            request: $request
        );
    }

    public function gallery(Product $product): JsonResponse
    {
        $this->authorize('view', $product);

        $gallery = $product->galleries()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'gallery' => ProductGalleryResource::collection($gallery)->resolve(),
        ]);
    }

    public function tagsIndex(Request $request): JsonResponse
    {
        abort_unless($request->user()?->can('products.view'), 403);

        $search = $request->input('search');

        $tags = ProductTag::query()
            ->when($search, function ($q) use ($search) {
                $term = '%'.$search.'%';
                $q->where('title->en', 'like', $term)
                    ->orWhere('title->ar', 'like', $term)
                    ->orWhere('slug', 'like', $term);
            })
            ->orderBy('title->'.app()->getLocale())
            ->limit(50)
            ->get();

        return response()->json([
            'tags' => ProductTagResource::collection($tags)->resolve(),
        ]);
    }

    public function tagsStore(StoreTagRequest $request): JsonResponse
    {
        $tag = ProductTag::create([
            'title' => array_filter($request->input('title', [])),
        ]);

        return $this->successResponse(
            data: [
                'tag' => (new ProductTagResource($tag))->resolve(),
            ],
            message: __('product::product.messages.tag_created'),
            status: 201,
            request: $request
        );
    }

    public function categories(Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();

        $categories = Category::query()
            ->whereNull('deleted_at')
            ->when($search, function ($q) use ($search) {
                $term = '%'.$search.'%';
                $q->where('title->en', 'like', $term)
                    ->orWhere('title->ar', 'like', $term);
            })
            ->orderBy('title->'.app()->getLocale())
            ->limit(25)
            ->get();

        return response()->json([
            'results' => $categories->map(fn ($category) => [
                'id' => $category->id,
                'text' => $category->title,
            ]),
        ]);
    }

    public function brands(Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();

        $brands = Brand::query()
            ->whereNull('deleted_at')
            ->when($search, function ($q) use ($search) {
                $term = '%'.$search.'%';
                $q->where('title->en', 'like', $term)
                    ->orWhere('title->ar', 'like', $term);
            })
            ->orderBy('title->'.app()->getLocale())
            ->limit(25)
            ->get();

        return response()->json([
            'results' => $brands->map(fn ($brand) => [
                'id' => $brand->id,
                'text' => $brand->title,
            ]),
        ]);
    }
}
