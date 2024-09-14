<?php

namespace App\Filament\Admin\Resources\UtilityType\Pages;

use App\Filament\Admin\Resources\UtilityType\UtilityTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUtilityType extends EditRecord
{
    protected static string $resource = UtilityTypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
        ];
    }
}
