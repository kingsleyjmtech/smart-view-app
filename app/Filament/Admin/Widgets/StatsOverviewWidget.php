<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Consumption\ConsumptionResource;
use App\Filament\Admin\Resources\Customer\CustomerResource;
use App\Filament\Admin\Resources\Meter\MeterResource;
use App\Filament\Admin\Resources\MeterReading\MeterReadingResource;
use App\Filament\Admin\Resources\MeterTariff\MeterTariffResource;
use App\Filament\Admin\Resources\Permission\PermissionResource;
use App\Filament\Admin\Resources\Role\RoleResource;
use App\Filament\Admin\Resources\Tariff\TariffResource;
use App\Filament\Admin\Resources\Tenant\TenantResource;
use App\Filament\Admin\Resources\User\UserResource;
use App\Filament\Admin\Resources\UtilityType\UtilityTypeResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Consumptions', ConsumptionResource::getEloquentQuery()->count()),
            Stat::make('Total Customers', CustomerResource::getEloquentQuery()->count()),
            Stat::make('Total Meter Readings', MeterReadingResource::getEloquentQuery()->count()),
            Stat::make('Total Meter Tariffs', MeterTariffResource::getEloquentQuery()->count()),
            Stat::make('Total Meters', MeterResource::getEloquentQuery()->count()),
            Stat::make('Total Permissions', PermissionResource::getEloquentQuery()->count()),
            Stat::make('Total Roles', RoleResource::getEloquentQuery()->count()),
            Stat::make('Total Tariffs', TariffResource::getEloquentQuery()->count()),
            Stat::make('Total Tenants', TenantResource::getEloquentQuery()->count()),
            Stat::make('Total Users', UserResource::getEloquentQuery()->count()),
            Stat::make('Total Utility Types', UtilityTypeResource::getEloquentQuery()->count()),
        ];
    }
}
