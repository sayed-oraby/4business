<?php

namespace Modules\Order\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Order\Http\Resources\OrderResource;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderStatus;
use Modules\Order\Repositories\OrderRepository;
use Modules\Order\Services\StatusService;

class OrderController extends Controller
{
    use ApiResponse;
    use AuthorizesRequests;

    public function __construct(protected OrderRepository $orders) {}

    public function index()
    {
        $this->authorize('viewAny', Order::class);

        $statuses = OrderStatus::orderBy('sort_order')->get();
        $statusesForJs = $statuses->map(function ($status) {
            return [
                'id' => $status->id,
                'code' => $status->code,
                'title' => $status->title, // Uses accessor for localized title
            ];
        })->values();

        return view('order::dashboard.orders.index', compact('statuses', 'statusesForJs'));
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        // Locale should already be set by ResolveLocale middleware
        // The OrderStatus accessor uses app()->getLocale() to return the correct translation

        $filters = $request->only(['status', 'payment_status', 'user_id', 'guest_uuid', 'search', 'date_from', 'date_to']);

        $orders = $this->orders->query($filters)
            ->with('status', 'shippingAddress')
            ->latest('created_at')
            ->paginate(20);

        return $this->successResponse(
            data: [
                'orders' => OrderResource::collection($orders)->response()->getData(true),
            ],
            message: __('order::messages.order_loaded'));
    }

    public function changeStatus(Request $request, Order $order, StatusService $statusService): JsonResponse
    {
        $this->authorize('update', $order);
        $request->validate([
            'status_id' => ['required', 'exists:order_statuses,id'],
            'comment' => ['nullable', 'string', 'max:500'],
        ], [
            'status_id.required' => __('order::validation.status_id.required'),
            'status_id.exists' => __('order::validation.status_id.exists'),
            'comment.string' => __('order::validation.comment.string'),
            'comment.max' => __('order::validation.comment.max', ['max' => 500]),
        ]);

        try {
            $status = OrderStatus::findOrFail($request->integer('status_id'));
            $order = $statusService->changeStatus($order, $status, $request->user('admin')?->id, $request->input('comment'));

            return $this->successResponse(
                data: ['order' => new OrderResource($order->load('shippingAddress'))],
                message: __('order::messages.status_changed'));
        } catch (\DomainException $e) {
            // Translate the exception message if it's a translation key
            $message = $e->getMessage();
            if (str_starts_with($message, 'order::messages.')) {
                $message = __($message);
            }

            return $this->errorResponse(
                message: $message,
                status: 422
            );
        }
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load([
            'status',
            'items',
            'addresses',
            'shippingAddress',
            'payments.logs',
            'statusHistory.status',
            'statusHistory' => function ($query) {
                $query->with('status')->latest('created_at');
            },
        ]);

        $statuses = OrderStatus::orderBy('sort_order')->get();
        $paymentStatuses = ['pending', 'paid', 'failed', 'cancelled', 'refunded'];

        return view('order::dashboard.orders.show', compact('order', 'statuses', 'paymentStatuses'));
    }

    public function edit(Order $order): View
    {
        $this->authorize('update', $order);

        $order->load([
            'status',
            'items',
            'addresses',
            'shippingAddress',
            'payments',
        ]);

        $statuses = OrderStatus::orderBy('sort_order')->get();
        $paymentStatuses = ['pending', 'paid', 'failed', 'cancelled', 'refunded'];

        return view('order::dashboard.orders.edit', compact('order', 'statuses', 'paymentStatuses'));
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'notes' => ['nullable', 'array'],
            'meta' => ['nullable', 'array'],
        ], [
            'notes.array' => __('order::validation.notes.array'),
            'meta.array' => __('order::validation.meta.array'),
        ]);

        $order->update($validated);

        return $this->successResponse(
            data: ['order' => new OrderResource($order->fresh(['status', 'shippingAddress']))],
            message: __('order::messages.order_updated'));
    }

    public function changePaymentStatus(Request $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'payment_status' => ['required', 'in:pending,paid,failed,cancelled,refunded'],
        ], [
            'payment_status.required' => __('order::validation.payment_status.required'),
            'payment_status.in' => __('order::validation.payment_status.in'),
        ]);

        $order->update([
            'payment_status' => $validated['payment_status'],
            'paid_at' => $validated['payment_status'] === 'paid' ? now() : null,
        ]);

        return $this->successResponse(
            data: ['order' => new OrderResource($order->fresh(['status', 'shippingAddress']))],
            message: __('order::messages.payment_status_changed'));
    }
}
