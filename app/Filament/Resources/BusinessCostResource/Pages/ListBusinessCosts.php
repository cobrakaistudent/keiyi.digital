<?php

namespace App\Filament\Resources\BusinessCostResource\Pages;

use App\Filament\Resources\BusinessCostResource;
use App\Models\BusinessCost;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBusinessCosts extends ListRecords
{
    protected static string $resource = BusinessCostResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
