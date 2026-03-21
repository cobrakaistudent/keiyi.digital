<?php

namespace App\Filament\Resources\BusinessCostResource\Pages;

use App\Filament\Resources\BusinessCostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBusinessCost extends EditRecord
{
    protected static string $resource = BusinessCostResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
