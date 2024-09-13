<?php

namespace App\Filament\Admin\Resources\Tariff\Pages;

use App\Filament\Admin\Resources\Tariff\TariffResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTariff extends ViewRecord
{
    protected static string $resource = TariffResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
