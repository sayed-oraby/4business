<?php

namespace Modules\User\Http\Resources;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class DataTableResource extends JsonResource
{
    /**
     * @param  array{draw:int,recordsTotal:int,recordsFiltered:int,data:Collection|EloquentCollection}  $resource
     */
    public function toArray($request): array
    {
        $rows = UserListItemResource::collection($this['data'])->resolve();

        return [
            'draw' => (int) ($this['draw'] ?? 0),
            'recordsTotal' => (int) $this['recordsTotal'],
            'recordsFiltered' => (int) $this['recordsFiltered'],
            'data' => $rows,
        ];
    }
}
