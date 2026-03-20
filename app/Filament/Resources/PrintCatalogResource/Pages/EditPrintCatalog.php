<?php
namespace App\Filament\Resources\PrintCatalogResource\Pages;
use App\Filament\Resources\PrintCatalogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
class EditPrintCatalog extends EditRecord {
    protected static string $resource = PrintCatalogResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
