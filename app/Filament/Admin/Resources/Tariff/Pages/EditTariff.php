<?php

namespace App\Filament\Admin\Resources\Tariff\Pages;

use App\Filament\Admin\Resources\Tariff\TariffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTariff extends EditRecord
{
    protected static string $resource = TariffResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
        ];
    }
}
