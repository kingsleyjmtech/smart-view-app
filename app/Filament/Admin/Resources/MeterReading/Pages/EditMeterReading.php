<?php

namespace App\Filament\Admin\Resources\MeterReading\Pages;

use App\Filament\Admin\Resources\MeterReading\MeterReadingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMeterReading extends EditRecord
{
    protected static string $resource = MeterReadingResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
        ];
    }
}
