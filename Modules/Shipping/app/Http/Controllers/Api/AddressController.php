<?php

namespace Modules\Shipping\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Shipping\Http\Requests\Api\StoreAddressRequest;
use Modules\Shipping\Http\Requests\Api\UpdateAddressRequest;
use Modules\Shipping\Http\Resources\AddressResource;
use Modules\Shipping\Models\ShippingCountry;
use Modules\Shipping\Models\UserAddress;

class AddressController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $addresses = UserAddress::query()
            ->where('user_id', $request->user()->id)
            ->with('country')
            ->orderByDesc('is_default_shipping')
            ->orderByDesc('is_default_billing')
            ->latest()
            ->get();

        return $this->successResponse(
            data: ['addresses' => AddressResource::collection($addresses)->resolve()],
            message: __('shipping::messages.addresses_loaded')
        );
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $address = $this->persist(new UserAddress(), $request->validated(), $request->user()->id);

        return $this->successResponse(
            data: ['address' => (new AddressResource($address->load('country')))->resolve()],
            message: __('shipping::messages.address_created'),
            status: 201
        );
    }

    public function update(UpdateAddressRequest $request, UserAddress $address): JsonResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);
        $address = $this->persist($address, $request->validated(), $request->user()->id);

        return $this->successResponse(
            data: ['address' => (new AddressResource($address->load('country')))->resolve()],
            message: __('shipping::messages.address_updated')
        );
    }

    public function destroy(Request $request, UserAddress $address): JsonResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);
        $address->delete();

        return $this->successResponse(
            data: null,
            message: __('shipping::messages.address_deleted')
        );
    }

    protected function persist(UserAddress $address, array $payload, int $userId): UserAddress
    {
        $payload['user_id'] = $userId;

        $countryIso = $address->country_iso2;

        if (isset($payload['shipping_country_id'])) {
            $countryIso = optional(ShippingCountry::find($payload['shipping_country_id']))->iso2 ?? $countryIso;
        }

        $payload['country_iso2'] = $countryIso;

        $address->fill($payload)->save();

        if ($address->is_default_shipping) {
            UserAddress::where('user_id', $address->user_id)
                ->where('id', '!=', $address->id)
                ->update(['is_default_shipping' => false]);
        }

        if ($address->is_default_billing) {
            UserAddress::where('user_id', $address->user_id)
                ->where('id', '!=', $address->id)
                ->update(['is_default_billing' => false]);
        }

        return $address;
    }
}
