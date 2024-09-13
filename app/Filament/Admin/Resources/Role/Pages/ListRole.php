<?php

namespace App\Filament\Admin\Resources\Role\Pages;

use App\Filament\Admin\Resources\Role\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRole extends ListRecords
{
    protected static string $resource = RoleResource::class;

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
