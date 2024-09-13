<?php

namespace App\Filament\Admin\Resources\MeterReading\Pages;

use App\Filament\Admin\Resources\MeterReading\MeterReadingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMeterReading extends ListRecords
{
    protected static string $resource = MeterReadingResource::class;

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
