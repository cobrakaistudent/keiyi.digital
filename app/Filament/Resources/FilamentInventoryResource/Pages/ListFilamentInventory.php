<?php

namespace App\Filament\Resources\FilamentInventoryResource\Pages;

use App\Filament\Resources\FilamentInventoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFilamentInventory extends ListRecords
{
    protected static string $resource = FilamentInventoryResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
