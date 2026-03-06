<?php

namespace App\Filament\Resources\AgencyProjectResource\Pages;

use App\Filament\Resources\AgencyProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAgencyProjects extends ManageRecords
{
    protected static string $resource = AgencyProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
