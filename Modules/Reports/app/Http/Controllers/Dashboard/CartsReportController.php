<?php

namespace Modules\Reports\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Cart\Models\Cart;
use Modules\Reports\Services\CartsReportService;

class CartsReportController extends Controller
{
    use ApiResponse;
    use AuthorizesRequests;

    public function __construct(protected CartsReportService $service)
    {
    }

    public function index(): \Illuminate\View\View
    {
        $stats = $this->service->stats();
        $abandoned = $this->service->topCarts(Cart::STATUS_ABANDONED, 10);
        $active = $this->service->topCarts(Cart::STATUS_ACTIVE, 10);
        $latest = $this->service->latest(10);

        return view('reports::dashboard.carts.index', compact('stats', 'abandoned', 'active', 'latest'));
    }

    public function notify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'channel' => 'required|in:email,fcm',
            'message' => 'required|string|max:500',
            'coupon_code' => 'nullable|string|max:100',
            'status' => 'nullable|string',
            'cart_id' => 'nullable|integer|exists:carts,id',
        ]);

        $query = Cart::query()->with('user');

        if (! empty($data['cart_id'])) {
            $query->where('id', $data['cart_id']);
        } elseif (! empty($data['status'])) {
            $query->where('status', $data['status']);
        } else {
            $query->whereIn('status', [Cart::STATUS_ACTIVE, Cart::STATUS_ABANDONED]);
        }

        $carts = $query->get();
        $sent = 0;

        foreach ($carts as $cart) {
            $payload = $data['message'];
            if (! empty($data['coupon_code'])) {
                $payload .= "\nCoupon: {$data['coupon_code']}";
            }

            if ($data['channel'] === 'email' && $cart->user?->email) {
                Mail::raw($payload, function ($mail) use ($cart) {
                    $mail->to($cart->user->email)->subject('We saved your cart');
                });
                $sent++;
            } elseif ($data['channel'] === 'fcm') {
                Log::info('FCM cart message', [
                    'cart_id' => $cart->id,
                    'user_id' => $cart->user_id,
                    'message' => $payload,
                ]);
                $sent++;
            }
        }

        return $this->successResponse(
            data: ['sent' => $sent],
            message: __('reports::dashboard.carts.notified')
        );
    }
}
