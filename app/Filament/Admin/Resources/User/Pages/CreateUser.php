<?php

namespace App\Filament\Admin\Resources\User\Pages;

use App\Filament\Admin\Resources\User\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
