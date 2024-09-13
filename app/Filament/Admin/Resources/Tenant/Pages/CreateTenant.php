<?php

namespace App\Filament\Admin\Resources\Tenant\Pages;

use App\Filament\Admin\Resources\Tenant\TenantResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;
}
