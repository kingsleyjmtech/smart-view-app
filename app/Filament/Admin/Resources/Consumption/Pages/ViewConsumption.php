<?php

namespace App\Filament\Admin\Resources\Consumption\Pages;

use App\Filament\Admin\Resources\Consumption\ConsumptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewConsumption extends ViewRecord
{
    protected static string $resource = ConsumptionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
