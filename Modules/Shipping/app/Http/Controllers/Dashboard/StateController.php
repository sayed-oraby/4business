<?php

namespace Modules\Shipping\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Shipping\Http\Requests\Dashboard\StoreStateRequest;
use Modules\Shipping\Http\Requests\Dashboard\UpdateStateRequest;
use Modules\Shipping\Models\ShippingCountry;
use Modules\Shipping\Models\ShippingState;

class StateController extends Controller
{
    use AuthorizesRequests;
    use ApiResponse;

    public function index()
    {
        $this->authorize('viewAny', ShippingCountry::class);

        $countries = ShippingCountry::query()
            ->orderBy('name_en')
            ->get();

        return view('shipping::dashboard.locations.index', compact('countries'));
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ShippingCountry::class);

        $countryId = $request->integer('country_id');
        $search = $request->input('search');

        $states = ShippingState::query()
            ->withCount('cities')
            ->with('country')
            ->when($countryId, fn ($q) => $q->where('shipping_country_id', $countryId))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('name_en')
            ->get();

        return $this->successResponse(
            data: ['states' => $states],
            message: __('shipping::messages.states_loaded')
        );
    }

    public function store(StoreStateRequest $request): JsonResponse
    {
        $state = ShippingState::create($request->validated());

        return $this->successResponse(
            data: ['state' => $state],
            message: __('shipping::messages.state_created'),
            status: 201
        );
    }

    public function update(UpdateStateRequest $request, ShippingState $state): JsonResponse
    {
        $state->update($request->validated());

        return $this->successResponse(
            data: ['state' => $state->fresh()],
            message: __('shipping::messages.state_updated')
        );
    }

    public function destroy(ShippingState $state): JsonResponse
    {
        $this->authorize('delete', $state->country);
        $state->delete();

        return $this->successResponse(
            data: null,
            message: __('shipping::messages.state_deleted')
        );
    }
}
