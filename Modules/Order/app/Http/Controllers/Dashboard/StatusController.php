<?php

namespace Modules\Order\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Order\Models\OrderStatus;

class StatusController extends Controller
{
    use ApiResponse;
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', OrderStatus::class);

        $availableLocales = [
            'en' => ['native' => 'English', 'name' => 'English'],
            'ar' => ['native' => 'العربية', 'name' => 'Arabic'],
        ];

        return view('order::dashboard.statuses.index', compact('availableLocales'));
    }

    public function data(Request $request): JsonResponse
    {
        // Locale should already be set by ResolveLocale middleware
        // The OrderStatus accessor uses app()->getLocale() to return the correct translation

        $statuses = OrderStatus::orderBy('sort_order')->get()->map(function ($status) {
            return [
                'id' => $status->id,
                'code' => $status->code,
                'title' => $status->title, // Uses accessor - returns localized title
                'color' => $status->color,
                'is_default' => $status->is_default,
                'is_final' => $status->is_final,
                'is_cancel' => $status->is_cancel,
                'is_refund' => $status->is_refund,
                'sort_order' => $status->sort_order,
                'title_translations' => $status->title_translations,
                'created_at' => $status->created_at,
                'updated_at' => $status->updated_at,
            ];
        });

        return $this->successResponse(
            data: ['statuses' => $statuses],
            message: __('order::messages.order_loaded'));
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', OrderStatus::class);
        $validated = $this->validatePayload($request);

        $status = OrderStatus::create($validated);

        return $this->successResponse(
            data: ['status' => $status],
            message: __('order::messages.status_created'),
            status: 201
        );
    }

    public function update(Request $request, OrderStatus $status): JsonResponse
    {
        $this->authorize('update', $status);
        $validated = $this->validatePayload($request, $status->id);

        $status->update($validated);

        return $this->successResponse(
            data: ['status' => $status->fresh()],
            message: __('order::messages.status_updated'));
    }

    public function destroy(OrderStatus $status): JsonResponse
    {
        $this->authorize('delete', $status);
        $status->delete();

        return $this->successResponse(
            data: null,
            message: __('order::messages.status_deleted'));
    }

    protected function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                \Illuminate\Validation\Rule::unique('order_statuses', 'code')->ignore($ignoreId),
            ],
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string', 'max:191'],
            'title.ar' => ['required', 'string', 'max:191'],
            'color' => ['nullable', 'string', 'max:20'],
            'is_default' => ['boolean'],
            'is_final' => ['boolean'],
            'is_cancel' => ['boolean'],
            'is_refund' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ], [
            'code.required' => __('order::validation.code.required'),
            'code.string' => __('order::validation.code.string'),
            'code.max' => __('order::validation.code.max', ['max' => 50]),
            'code.unique' => __('order::validation.code.unique'),
            'title.required' => __('order::validation.title.required'),
            'title.array' => __('order::validation.title.array'),
            'title.en.required' => __('order::validation.title.en.required'),
            'title.en.string' => __('order::validation.title.string'),
            'title.en.max' => __('order::validation.title.max', ['max' => 191]),
            'title.ar.required' => __('order::validation.title.ar.required'),
            'title.ar.string' => __('order::validation.title.string'),
            'title.ar.max' => __('order::validation.title.max', ['max' => 191]),
            'color.string' => __('order::validation.color.string'),
            'color.max' => __('order::validation.color.max', ['max' => 20]),
            'sort_order.integer' => __('order::validation.sort_order.integer'),
            'sort_order.min' => __('order::validation.sort_order.min', ['min' => 0]),
        ]);
    }
}
