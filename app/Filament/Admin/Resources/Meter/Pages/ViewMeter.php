<?php

namespace App\Filament\Admin\Resources\Meter\Pages;

use App\Filament\Admin\Resources\Meter\MeterResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMeter extends ViewRecord
{
    protected static string $resource = MeterResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
