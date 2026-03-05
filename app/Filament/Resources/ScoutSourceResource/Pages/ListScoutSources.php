<?php

namespace App\Filament\Resources\ScoutSourceResource\Pages;

use App\Filament\Resources\ScoutSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScoutSources extends ListRecords
{
    protected static string $resource = ScoutSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
