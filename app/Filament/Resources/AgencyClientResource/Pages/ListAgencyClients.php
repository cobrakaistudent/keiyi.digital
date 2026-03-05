<?php

namespace App\Filament\Resources\AgencyClientResource\Pages;

use App\Filament\Resources\AgencyClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgencyClients extends ListRecords
{
    protected static string $resource = AgencyClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
