<?php

namespace App\Filament\Admin\Resources\Meter\Pages;

use App\Filament\Admin\Resources\Meter\MeterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMeter extends EditRecord
{
    protected static string $resource = MeterResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
        ];
    }
}
