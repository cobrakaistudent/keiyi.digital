<?php

namespace App\Filament\Resources\AgencyClientResource\Pages;

use App\Filament\Resources\AgencyClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgencyClient extends EditRecord
{
    protected static string $resource = AgencyClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
