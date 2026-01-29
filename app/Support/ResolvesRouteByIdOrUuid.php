<?php

namespace App\Support;

/**
 * Trait to support both ID and UUID for route model binding.
 * 
 * Use this trait in models that have both 'id' and 'uuid' columns
 * and need to support route binding by either field.
 */
trait ResolvesRouteByIdOrUuid
{
    /**
     * Resolve the model for route model binding (supports both ID and UUID).
     * 
     * @param mixed $value The route parameter value
     * @param string|null $field Optional specific field to search by
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // If a specific field is requested, use it
        if ($field) {
            return $this->where($field, $value)->first();
        }

        // Get the UUID column name (default 'uuid', can be overridden)
        $uuidColumn = $this->getUuidColumn();

        // Try to find by ID first (if numeric), then by UUID
        if (is_numeric($value)) {
            return $this->where('id', $value)->first() 
                ?? $this->where($uuidColumn, $value)->first();
        }

        // If not numeric, search by UUID
        return $this->where($uuidColumn, $value)->first();
    }

    /**
     * Resolve the model for child route model binding.
     */
    public function resolveChildRouteBinding($childType, $value, $field)
    {
        return $this->{$childType}()->where($field ?? 'id', $value)->first();
    }

    /**
     * Get the UUID column name. Override this in your model if needed.
     */
    protected function getUuidColumn(): string
    {
        return 'uuid';
    }
}
