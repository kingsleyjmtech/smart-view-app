<?php

namespace App\Policies;

class TariffPolicy
{
    /**
     * Determine whether the user can viewAny the model.
     */
    public function viewAny(): bool
    {
        return auth()->user()->hasPermission('tariff_access');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(): bool
    {
        return auth()->user()->hasPermission('tariff_show');
    }

    /**
     * Determine whether the user can create the model.
     */
    public function create(): bool
    {
        return auth()->user()->hasPermission('tariff_create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(): bool
    {
        return auth()->user()->hasPermission('tariff_edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(): bool
    {
        return auth()->user()->hasPermission('tariff_delete');
    }

    /**
     * Determine whether the user can deleteAny the model.
     */
    public function deleteAny(): bool
    {
        return auth()->user()->hasPermission('tariff_delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(): bool
    {
        return auth()->user()->hasPermission('tariff_delete');
    }

    /**
     * Determine whether the user can restoreAny the model.
     */
    public function restoreAny(): bool
    {
        return auth()->user()->hasPermission('tariff_delete');
    }

    /**
     * Determine whether the user can forceDelete the model.
     */
    public function forceDelete(): bool
    {
        return auth()->user()->hasPermission('tariff_delete');
    }

    /**
     * Determine whether the user can forceDeleteAny the model.
     */
    public function forceDeleteAny(): bool
    {
        return auth()->user()->hasPermission('tariff_delete');
    }
}
