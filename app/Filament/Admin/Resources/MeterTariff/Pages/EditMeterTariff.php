<?php

namespace App\Filament\Admin\Resources\MeterTariff\Pages;

use App\Filament\Admin\Resources\MeterTariff\MeterTariffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMeterTariff extends EditRecord
{
    protected static string $resource = MeterTariffResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
        ];
    }
}
