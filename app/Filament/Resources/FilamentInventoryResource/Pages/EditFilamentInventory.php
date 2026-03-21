<?php

namespace App\Filament\Resources\FilamentInventoryResource\Pages;

use App\Filament\Resources\FilamentInventoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFilamentInventory extends EditRecord
{
    protected static string $resource = FilamentInventoryResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
