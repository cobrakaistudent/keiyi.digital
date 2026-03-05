<?php

namespace App\Filament\Resources\ScoutSourceResource\Pages;

use App\Filament\Resources\ScoutSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScoutSource extends EditRecord
{
    protected static string $resource = ScoutSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
