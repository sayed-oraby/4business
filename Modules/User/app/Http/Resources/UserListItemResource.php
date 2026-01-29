<?php

namespace Modules\User\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserListItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'roles' => $this->roles->pluck('name')->values()->all(),
            'status' => $this->deleted_at ? 'deleted' : 'active',
            'avatar' => $this->avatar_url,
        ];
    }
}
