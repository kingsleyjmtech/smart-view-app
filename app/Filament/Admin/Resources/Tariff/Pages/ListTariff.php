<?php

namespace App\Filament\Admin\Resources\Tariff\Pages;

use App\Filament\Admin\Resources\Tariff\TariffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTariff extends ListRecords
{
    protected static string $resource = TariffResource::class;

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
