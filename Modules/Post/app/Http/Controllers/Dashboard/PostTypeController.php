<?php

namespace Modules\Post\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;
use Modules\Post\Models\PostType;
use Modules\Setting\Services\Media\MediaUploader;

class PostTypeController extends Controller
{
    use ApiResponse;

    public function __construct(protected MediaUploader $uploader) {}

    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $types = PostType::latest()->get()->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'name_translations' => $type->getTranslations('name'),
                    'slug' => $type->slug,
                    'image_path' => $type->image_path,
                    'image_url' => $type->image_url,
                    'status' => $type->status,
                    'status_label' => $type->status ? __('post::post.types.statuses.active') : __('post::post.types.statuses.inactive'),
                ];
            });
            return $this->successResponse(data: $types);
        }
        return view('post::dashboard.types.index');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string',
            'name.ar' => 'required|string',
            'image' => 'nullable|image|max:4096',
            'status' => 'boolean',
        ]);

        $validated['slug'] = str()->slug($validated['name']['en']);
        
        // Ensure slug is unique
        $originalSlug = $validated['slug'];
        $count = 1;
        while (PostType::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        // Handle image upload
        if ($request->hasFile('image') && $request->file('image') instanceof UploadedFile) {
            $validated['image_path'] = $this->uploader->upload($request->file('image'), 'post-types', [
                'max_width' => 400,
            ])->path();
        }
        unset($validated['image']);

        $type = PostType::create($validated);

        return $this->successResponse(
            data: ['type' => [
                'id' => $type->id,
                'name' => $type->name,
                'name_translations' => $type->getTranslations('name'),
                'slug' => $type->slug,
                'image_path' => $type->image_path,
                'image_url' => $type->image_url,
                'status' => $type->status,
                'status_label' => $type->status ? __('post::post.types.statuses.active') : __('post::post.types.statuses.inactive'),
            ]],
            message: __('post::post.messages.created'),
            status: 201
        );
    }

    public function update(Request $request, PostType $postType): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|array',
            'name.en' => 'sometimes|string',
            'name.ar' => 'sometimes|string',
            'image' => 'nullable|image|max:4096',
            'status' => 'boolean',
        ]);

        if (isset($validated['name']['en']) && $validated['name']['en'] !== $postType->getTranslation('name', 'en')) {
             $validated['slug'] = str()->slug($validated['name']['en']);
             // Ensure slug is unique excluding current
            $originalSlug = $validated['slug'];
            $count = 1;
            while (PostType::where('slug', $validated['slug'])->where('id', '!=', $postType->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        // Handle image upload
        if ($request->hasFile('image') && $request->file('image') instanceof UploadedFile) {
            $validated['image_path'] = $this->uploader->upload($request->file('image'), 'post-types', [
                'max_width' => 400,
            ])->path();
        }
        unset($validated['image']);

        $postType->update($validated);

        return $this->successResponse(
            data: ['type' => [
                'id' => $postType->id,
                'name' => $postType->name,
                'name_translations' => $postType->getTranslations('name'),
                'slug' => $postType->slug,
                'image_path' => $postType->image_path,
                'image_url' => $postType->image_url,
                'status' => $postType->status,
                'status_label' => $postType->status ? __('post::post.types.statuses.active') : __('post::post.types.statuses.inactive'),
            ]],
            message: __('post::post.messages.updated')
        );
    }

    public function destroy(PostType $postType): JsonResponse
    {
        $postType->delete();

        return $this->successResponse(
            message: __('post::post.messages.deleted')
        );
    }
}
