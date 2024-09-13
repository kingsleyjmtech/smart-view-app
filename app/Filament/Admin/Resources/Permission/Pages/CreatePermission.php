<?php

namespace App\Filament\Admin\Resources\Permission\Pages;

use App\Filament\Admin\Resources\Permission\PermissionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;
}
