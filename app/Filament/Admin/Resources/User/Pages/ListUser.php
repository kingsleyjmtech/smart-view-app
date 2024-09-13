<?php

namespace App\Filament\Admin\Resources\User\Pages;

use App\Filament\Admin\Resources\User\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUser extends ListRecords
{
    protected static string $resource = UserResource::class;

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
