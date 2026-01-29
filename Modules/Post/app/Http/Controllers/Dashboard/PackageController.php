<?php

namespace Modules\Post\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Modules\Post\Models\Package;

class PackageController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $packages = Package::latest()->get()->map(function ($package) {
                return [
                    'id' => $package->id,
                    'title' => $package->title,
                    'title_translations' => $package->getTranslations('title'),
                    'description' => $package->description,
                    'description_translations' => $package->getTranslations('description'),
                    'price' => $package->price,
                    'period_days' => $package->period_days,
                    'top_days' => $package->top_days,
                    'free_days' => $package->free_days,
                    'label_color' => $package->label_color,
                    'card_color' => $package->card_color,
                    'cover_image' => $package->cover_image,
                    'cover_image_url' => $package->cover_image_url,
                    'status' => $package->status,
                    'status_label' => $package->status ? __('post::post.statuses.active') : __('post::post.statuses.inactive'),
                    'is_featured' => $package->is_featured,
                    'is_free' => $package->is_free,
                    'free_credits_per_user' => $package->free_credits_per_user,
                ];
            });

            return $this->successResponse(data: $packages);
        }

        return view('post::dashboard.packages.index');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'title.en' => 'required|string',
            'title.ar' => 'required|string',
            'price' => 'required|numeric|min:0',
            'period_days' => 'required|integer|min:1',
            'top_days' => 'nullable|integer|min:0',
            'label_color' => 'nullable|string|max:7',
            'card_color' => 'nullable|string|max:7',
            'description' => 'nullable|array',
            'status' => 'boolean',
            'is_featured' => 'boolean',
            'is_free' => 'boolean',
            'free_credits_per_user' => 'nullable|integer|min:1',
            'cover_image' => 'nullable|image',
        ]);

        // Validate free package constraints
        $isFree = filter_var($validated['is_free'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $isActive = filter_var($validated['status'] ?? true, FILTER_VALIDATE_BOOLEAN);

        if ($isFree && $isActive && Package::hasActiveFreePackage()) {
            throw ValidationException::withMessages([
                'is_free' => [__('post::post.packages.validation.free_package_exists')],
            ]);
        }

        // Free package must have 0 price
        if ($isFree) {
            $validated['price'] = 0;
            if (empty($validated['free_credits_per_user'])) {
                throw ValidationException::withMessages([
                    'free_credits_per_user' => [__('post::post.packages.validation.free_credits_required')],
                ]);
            }
        }

        // Set default for top_days if not provided
        if (empty($validated['top_days'])) {
            $validated['top_days'] = 0;
        }

        // Ensure top_days doesn't exceed period_days
        if ($validated['top_days'] > $validated['period_days']) {
            $validated['top_days'] = $validated['period_days'];
        }

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('packages', 'public');
        }

        $package = Package::create($validated);

        return $this->successResponse(
            data: ['package' => $this->formatPackage($package)],
            message: __('post::post.messages.created'),
            status: 201
        );
    }

    public function update(Request $request, Package $package): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|array',
            'price' => 'sometimes|numeric|min:0',
            'period_days' => 'sometimes|integer|min:1',
            'top_days' => 'nullable|integer|min:0',
            'label_color' => 'nullable|string|max:7',
            'card_color' => 'nullable|string|max:7',
            'description' => 'nullable|array',
            'status' => 'boolean',
            'is_featured' => 'boolean',
            'is_free' => 'boolean',
            'free_credits_per_user' => 'nullable|integer|min:1',
            'cover_image' => 'nullable|image',
        ]);

        // Validate free package constraints
        $isFree = filter_var($validated['is_free'] ?? $package->is_free, FILTER_VALIDATE_BOOLEAN);
        $isActive = filter_var($validated['status'] ?? $package->status, FILTER_VALIDATE_BOOLEAN);

        if ($isFree && $isActive && Package::hasActiveFreePackage($package->id)) {
            throw ValidationException::withMessages([
                'is_free' => [__('post::post.packages.validation.free_package_exists')],
            ]);
        }

        // Free package must have 0 price
        if ($isFree) {
            $validated['price'] = 0;
            if (empty($validated['free_credits_per_user']) && empty($package->free_credits_per_user)) {
                throw ValidationException::withMessages([
                    'free_credits_per_user' => [__('post::post.packages.validation.free_credits_required')],
                ]);
            }
        }

        // Set default for top_days if not provided
        $periodDays = $validated['period_days'] ?? $package->period_days;
        if (! isset($validated['top_days']) || $validated['top_days'] === null || $validated['top_days'] === '') {
            $validated['top_days'] = $package->top_days ?? 0;
        }

        // Ensure top_days doesn't exceed period_days
        if ($validated['top_days'] > $periodDays) {
            $validated['top_days'] = $periodDays;
        }

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('packages', 'public');
        }

        $package->update($validated);

        return $this->successResponse(
            data: ['package' => $this->formatPackage($package->fresh())],
            message: __('post::post.messages.updated')
        );
    }

    public function destroy(Package $package): JsonResponse
    {
        $package->delete();

        return $this->successResponse(
            message: __('post::post.messages.deleted')
        );
    }

    protected function formatPackage(Package $package): array
    {
        return [
            'id' => $package->id,
            'title' => $package->title,
            'title_translations' => $package->getTranslations('title'),
            'description' => $package->description,
            'description_translations' => $package->getTranslations('description'),
            'price' => $package->price,
            'period_days' => $package->period_days,
            'top_days' => $package->top_days,
            'free_days' => $package->free_days,
            'label_color' => $package->label_color,
            'card_color' => $package->card_color,
            'cover_image' => $package->cover_image,
            'cover_image_url' => $package->cover_image_url,
            'status' => $package->status,
            'status_label' => $package->status ? __('post::post.statuses.active') : __('post::post.statuses.inactive'),
            'is_featured' => $package->is_featured,
            'is_free' => $package->is_free,
            'free_credits_per_user' => $package->free_credits_per_user,
        ];
    }
}
