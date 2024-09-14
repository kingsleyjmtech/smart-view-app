<?php

namespace App\Filament\Admin\Resources\UtilityType\Pages;

use App\Filament\Admin\Resources\UtilityType\UtilityTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUtilityType extends ViewRecord
{
    protected static string $resource = UtilityTypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
