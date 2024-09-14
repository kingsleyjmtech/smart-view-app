<?php

namespace App\Filament\Admin\Resources\Consumption\Pages;

use App\Filament\Admin\Resources\Consumption\ConsumptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConsumption extends EditRecord
{
    protected static string $resource = ConsumptionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
        ];
    }
}
