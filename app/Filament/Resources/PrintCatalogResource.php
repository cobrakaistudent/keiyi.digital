<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrintCatalogResource\Pages;
use App\Models\PrintCatalog;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrintCatalogResource extends Resource
{
    protected static ?string $model = PrintCatalog::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Catálogo 3D';
    protected static ?string $navigationGroup = '3D World';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')->label('Título')->required()->maxLength(255)->columnSpanFull(),
            Textarea::make('description')->label('Descripción')->rows(3)->columnSpanFull(),
            TextInput::make('embed_url')->label('URL embed (Instagram/TikTok)')->url()->columnSpanFull(),
            FileUpload::make('file_path')
                ->label('Archivo 3D (STL/OBJ/3MF)')
                ->disk('local')
                ->directory('catalog/models')
                ->acceptedFileTypes(['model/stl', 'application/sla', 'text/plain', 'application/octet-stream'])
                ->maxSize(51200)
                ->storeFileNamesIn('file_name')
                ->columnSpanFull(),
            TextInput::make('price')->label('Precio (MXN)')->numeric()->prefix('$'),
            Select::make('material')->label('Material')->options([
                'PLA'    => 'PLA',
                'PETG'   => 'PETG',
                'TPU'    => 'TPU',
                'Resina' => 'Resina',
                'Otro'   => 'Otro',
            ]),
            TextInput::make('print_time')->label('Tiempo de impresión')->placeholder('Ej: 2-3 horas'),
            Toggle::make('downloadable')->label('Disponible para descarga')->default(true),
            Toggle::make('orderable')->label('Disponible para solicitar impresión')->default(true),
            Toggle::make('active')->label('Activo en galería')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Título')->searchable()->limit(40),
                TextColumn::make('material')->label('Material')->badge(),
                TextColumn::make('price')->label('Precio')->money('MXN'),
                TextColumn::make('print_time')->label('Tiempo'),
                IconColumn::make('downloadable')->label('Descarga')->boolean(),
                IconColumn::make('orderable')->label('Pedido')->boolean(),
                IconColumn::make('active')->label('Activo')->boolean(),
                TextColumn::make('created_at')->label('Creado')->since()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPrintCatalogs::route('/'),
            'create' => Pages\CreatePrintCatalog::route('/create'),
            'edit'   => Pages\EditPrintCatalog::route('/{record}/edit'),
        ];
    }
}
