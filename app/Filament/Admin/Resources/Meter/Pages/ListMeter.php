<?php

namespace App\Filament\Admin\Resources\Meter\Pages;

use App\Filament\Admin\Resources\Meter\MeterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMeter extends ListRecords
{
    protected static string $resource = MeterResource::class;

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
