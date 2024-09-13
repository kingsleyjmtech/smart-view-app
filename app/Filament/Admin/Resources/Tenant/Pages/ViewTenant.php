<?php

namespace App\Filament\Admin\Resources\Tenant\Pages;

use App\Filament\Admin\Resources\Tenant\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTenant extends ViewRecord
{
    protected static string $resource = TenantResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
