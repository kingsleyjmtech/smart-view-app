<?php

namespace App\Filament\Admin\Resources\MeterTariff\Pages;

use App\Filament\Admin\Resources\MeterTariff\MeterTariffResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMeterTariff extends ViewRecord
{
    protected static string $resource = MeterTariffResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
