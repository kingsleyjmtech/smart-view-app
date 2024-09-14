<?php

namespace App\Filament\Admin\Resources\Consumption\Pages;

use App\Filament\Admin\Resources\Consumption\ConsumptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsumption extends ListRecords
{
    protected static string $resource = ConsumptionResource::class;

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
