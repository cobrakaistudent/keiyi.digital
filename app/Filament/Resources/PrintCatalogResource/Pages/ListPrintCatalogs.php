<?php
namespace App\Filament\Resources\PrintCatalogResource\Pages;
use App\Filament\Resources\PrintCatalogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListPrintCatalogs extends ListRecords {
    protected static string $resource = PrintCatalogResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
