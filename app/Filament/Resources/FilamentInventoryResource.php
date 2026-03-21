<?php

namespace App\Filament\Resources;

use App\Models\FilamentInventory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FilamentInventoryResource extends Resource
{
    protected static ?string $model = FilamentInventory::class;
    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationLabel = 'Inventario Filamentos';
    protected static ?string $navigationGroup = '3D World';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(3)->schema([
                TextInput::make('brand')->label('Marca')->required()->placeholder('eSUN, Bambu, Hatchbox...'),
                Select::make('material')->label('Material')->required()->options([
                    'PLA'  => 'PLA', 'PETG' => 'PETG', 'TPU' => 'TPU',
                    'ABS'  => 'ABS', 'ASA'  => 'ASA',  'Resina' => 'Resina',
                    'Nylon' => 'Nylon', 'Otro' => 'Otro',
                ]),
                TextInput::make('color')->label('Color')->required()->placeholder('Negro, Blanco, Rojo...'),
            ]),
            Grid::make(3)->schema([
                TextInput::make('weight_grams')->label('Peso spool (g)')->numeric()->default(1000),
                TextInput::make('remaining_grams')->label('Restante (g)')->numeric()->default(1000),
                TextInput::make('cost_per_kg')->label('Costo por kg (MXN)')->numeric()->prefix('$')->default(450),
            ]),
            Grid::make(3)->schema([
                Select::make('diameter')->label('Diámetro')->options([
                    '1.75mm' => '1.75mm', '2.85mm' => '2.85mm',
                ])->default('1.75mm'),
                Select::make('status')->label('Estado')->options([
                    'active' => 'Activo', 'low' => 'Bajo', 'empty' => 'Vacío',
                ])->default('active'),
                DatePicker::make('purchased_at')->label('Fecha de compra'),
            ]),
            Textarea::make('notes')->label('Notas')->rows(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('brand')->label('Marca')->searchable(),
                TextColumn::make('material')->label('Material')->badge()
                    ->color(fn (string $state) => match($state) {
                        'PLA'    => 'success',
                        'PETG'   => 'info',
                        'TPU'    => 'warning',
                        'Resina' => 'danger',
                        default  => 'gray',
                    }),
                TextColumn::make('color')->label('Color'),
                TextColumn::make('remaining_grams')
                    ->label('Restante')
                    ->formatStateUsing(fn ($state, $record) => "{$state}g / {$record->weight_grams}g ({$record->remaining_percent}%)")
                    ->color(fn ($record) => $record->remaining_percent < 20 ? 'danger' : ($record->remaining_percent < 50 ? 'warning' : 'success')),
                TextColumn::make('cost_per_kg')->label('$/kg')->money('MXN'),
                TextColumn::make('status')->label('Estado')->badge()
                    ->color(fn (string $state) => match($state) {
                        'active' => 'success', 'low' => 'warning', 'empty' => 'danger', default => 'gray',
                    }),
                TextColumn::make('purchased_at')->label('Comprado')->date('d M Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Resources\FilamentInventoryResource\Pages\ListFilamentInventory::route('/'),
            'create' => \App\Filament\Resources\FilamentInventoryResource\Pages\CreateFilamentInventory::route('/create'),
            'edit'   => \App\Filament\Resources\FilamentInventoryResource\Pages\EditFilamentInventory::route('/{record}/edit'),
        ];
    }
}
