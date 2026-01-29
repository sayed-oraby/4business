<?php

namespace Modules\User\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        $lang = app()->getLocale();

        return [
            'id' => $this->id,
            'account_type' => $this->account_type,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->mobile,
            // 'company_name' => $this->company_name,
            // 'address' => $this->address,
            // 'birthdate' => optional($this->birthdate)->toDateString(),
            // 'gender' => $this->gender,
            'avatar_url' => $this->avatar_url,
            'state'      => $this->state != null ? [
                'id' => $this->state->id,
                'name' => $this->state->{'name_'.$lang}
            ] : null,
            'city'      => $this->city != null ? [
                'id' => $this->city->id,
                'name' => $this->city->{'name_'.$lang}
            ] : null,

        ];
    }
}
