<?php

namespace App\Filament\Admin\Resources\MeterReading\Pages;

use App\Filament\Admin\Resources\MeterReading\MeterReadingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMeterReading extends ViewRecord
{
    protected static string $resource = MeterReadingResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
