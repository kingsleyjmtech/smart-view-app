<?php

namespace App\Traits\Traits\Shared;

use Illuminate\Database\Eloquent\Builder;

trait HasStatus
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', 'inactive');
    }

    public function activate(): mixed
    {
        $this->status = 'active';

        return $this->save();
    }

    public function deactivate(): mixed
    {
        $this->status = 'inactive';

        return $this->save();
    }

    public function isActive(): bool
    {
        return $this->status === 'Active';
    }

    public function isInactive(): bool
    {
        return $this->status === 'Inactive';
    }
}
