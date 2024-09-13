<?php

namespace App\Filament\Admin\Resources\MeterTariff\Pages;

use App\Filament\Admin\Resources\MeterTariff\MeterTariffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMeterTariff extends ListRecords
{
    protected static string $resource = MeterTariffResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function setPage($page, $pageName = 'page'): void
    {
        parent::setPage($page, $pageName);

        $this->dispatch('scroll-to-top');
    }
}
