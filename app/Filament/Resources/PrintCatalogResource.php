<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrintCatalogResource\Pages;
use App\Models\PrintCatalog;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
            Section::make('Información del modelo')->schema([
                TextInput::make('title')
                    ->label('Nombre del modelo')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(3)
                    ->columnSpanFull(),
                Select::make('category')
                    ->label('Categoría')
                    ->options([
                        'figurine'   => 'Figurina / Personaje',
                        'tool'       => 'Herramienta / Funcional',
                        'decoration' => 'Decoración / Arte',
                        'mechanical' => 'Mecánico / Ingeniería',
                        'other'      => 'Otro',
                    ]),
            ]),

            Section::make('Archivos')->schema([
                FileUpload::make('image_path')
                    ->label('Imagen de preview')
                    ->image()
                    ->disk('public')
                    ->directory('catalog/images')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('4:3')
                    ->imageResizeTargetWidth('800')
                    ->imageResizeTargetHeight('600')
                    ->columnSpanFull(),
                FileUpload::make('file_path')
                    ->label('Archivo 3D (STL/OBJ/3MF)')
                    ->disk('local')
                    ->directory('catalog/models')
                    ->maxSize(51200)
                    ->storeFileNamesIn('file_name')
                    ->columnSpanFull(),
                TextInput::make('embed_url')
                    ->label('URL embed (Instagram/TikTok/YouTube)')
                    ->url()
                    ->columnSpanFull(),
            ]),

            Section::make('Detalles de impresión')->schema([
                Grid::make(3)->schema([
                    TextInput::make('price')
                        ->label('Precio (USD)')
                        ->numeric()
                        ->prefix('$'),
                    Select::make('material')
                        ->label('Material')
                        ->options([
                            'PLA'    => 'PLA',
                            'PETG'   => 'PETG',
                            'TPU'    => 'TPU',
                            'Resina' => 'Resina',
                            'Otro'   => 'Otro',
                        ]),
                    TextInput::make('print_time')
                        ->label('Tiempo estimado')
                        ->placeholder('Ej: 2-3 horas'),
                ]),
            ]),

            Section::make('Opciones de publicación')->schema([
                Grid::make(4)->schema([
                    Toggle::make('downloadable')
                        ->label('Descargable')
                        ->default(true),
                    Toggle::make('orderable')
                        ->label('Cotizable')
                        ->default(true),
                    Toggle::make('active')
                        ->label('Activo')
                        ->default(true),
                    Select::make('status')
                        ->label('Estado')
                        ->options([
                            'draft'     => 'Borrador',
                            'published' => 'Publicado',
                        ])
                        ->default('draft'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->size(40),
                TextColumn::make('title')
                    ->label('Modelo')
                    ->searchable()
                    ->limit(35),
                TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'figurine'   => 'Figurina',
                        'tool'       => 'Herramienta',
                        'decoration' => 'Decoración',
                        'mechanical' => 'Mecánico',
                        default      => $state ?? '—',
                    }),
                TextColumn::make('material')->label('Material')->badge(),
                TextColumn::make('price')->label('Precio')->money('USD'),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'published' => 'success',
                        'draft'     => 'warning',
                        default     => 'gray',
                    }),
                IconColumn::make('downloadable')->label('⬇')->boolean(),
                IconColumn::make('orderable')->label('🖨')->boolean(),
                TextColumn::make('download_tokens_count')
                    ->label('Descargas')
                    ->counts('downloadTokens')
                    ->sortable(),
                TextColumn::make('created_at')->label('Creado')->since()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Borrador',
                        'published' => 'Publicado',
                    ]),
            ])
            ->actions([
                Action::make('publish')
                    ->label('Publicar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('¿Aprobar y publicar este modelo?')
                    ->modalDescription('El modelo será visible en la galería pública de 3D World.')
                    ->action(fn (PrintCatalog $record) => $record->update(['status' => 'published']))
                    ->visible(fn (PrintCatalog $record) => $record->status === 'draft'),
                Action::make('unpublish')
                    ->label('Despublicar')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (PrintCatalog $record) => $record->update(['status' => 'draft']))
                    ->visible(fn (PrintCatalog $record) => $record->status === 'published'),
                EditAction::make(),
                DeleteAction::make(),
            ]);
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
