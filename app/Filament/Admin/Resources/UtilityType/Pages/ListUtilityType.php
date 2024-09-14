<?php

namespace App\Filament\Admin\Resources\UtilityType\Pages;

use App\Filament\Admin\Resources\UtilityType\UtilityTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUtilityType extends ListRecords
{
    protected static string $resource = UtilityTypeResource::class;

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
