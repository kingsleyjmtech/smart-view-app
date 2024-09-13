<?php

namespace App\Filament\Admin\Resources\Tenant\Pages;

use App\Filament\Admin\Resources\Tenant\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenant extends ListRecords
{
    protected static string $resource = TenantResource::class;

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
