<?php

namespace App\Filament\Admin\Resources\Customer\Pages;

use App\Filament\Admin\Resources\Customer\CustomerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
}
