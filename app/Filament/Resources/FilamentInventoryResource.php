<?php

namespace App\Filament\Resources;

use App\Models\FilamentInventory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
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
            Section::make('Datos del filamento')->schema([
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
            ]),

            Section::make('Compra')->schema([
                Grid::make(3)->schema([
                    Select::make('store')->label('Tienda')->options([
                        'Amazon'       => 'Amazon',
                        'MercadoLibre' => 'MercadoLibre',
                        'Fisica'       => 'Tienda física',
                        'Otro'         => 'Otro',
                    ]),
                    DatePicker::make('purchased_at')->label('Fecha de compra'),
                    Select::make('diameter')->label('Diámetro')->options([
                        '1.75mm' => '1.75mm', '2.85mm' => '2.85mm',
                    ])->default('1.75mm'),
                ]),
                TextInput::make('purchase_url')->label('Link de compra (Amazon, MercadoLibre, etc.)')->url()->columnSpanFull(),
            ]),

            Section::make('Estado')->schema([
                Grid::make(2)->schema([
                    Select::make('status')->label('Estado')->options([
                        'active' => 'Activo', 'low' => 'Bajo', 'empty' => 'Vacío',
                    ])->default('active'),
                    Select::make('source')->label('Origen del dato')->options([
                        'manual'     => 'Manual (ticket físico)',
                        'url_scrape' => 'Extraído de URL',
                    ])->default('manual'),
                ]),
                Textarea::make('notes')->label('Notas')->rows(2)->columnSpanFull(),
            ]),
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
                TextColumn::make('store')->label('Tienda')->badge()->color('gray'),
                TextColumn::make('status')->label('Estado')->badge()
                    ->color(fn (string $state) => match($state) {
                        'active' => 'success', 'low' => 'warning', 'empty' => 'danger', default => 'gray',
                    }),
                TextColumn::make('purchased_at')->label('Comprado')->date('d M Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                // Botón para importar desde URL
                Action::make('import_from_url')
                    ->label('Importar desde URL')
                    ->icon('heroicon-o-link')
                    ->color('info')
                    ->form([
                        TextInput::make('url')
                            ->label('Pega el link de Amazon o MercadoLibre')
                            ->url()
                            ->required()
                            ->placeholder('https://www.amazon.com.mx/dp/...'),
                    ])
                    ->action(function (array $data) {
                        $scraped = FilamentInventory::scrapeFromUrl($data['url']);

                        // Crear registro con datos extraídos (el usuario puede editar después)
                        $item = FilamentInventory::create(array_merge([
                            'brand'           => $scraped['brand'] ?? 'Desconocida',
                            'material'        => $scraped['material'] ?? 'PLA',
                            'color'           => $scraped['color'] ?? 'Sin especificar',
                            'weight_grams'    => $scraped['weight_grams'] ?? 1000,
                            'remaining_grams' => $scraped['remaining_grams'] ?? $scraped['weight_grams'] ?? 1000,
                            'cost_per_kg'     => $scraped['cost_per_kg'] ?? 450,
                            'diameter'        => '1.75mm',
                            'status'          => 'active',
                            'purchased_at'    => now(),
                            'purchase_url'    => $scraped['purchase_url'] ?? $data['url'],
                            'store'           => $scraped['store'] ?? 'Online',
                            'source'          => 'url_scrape',
                            'notes'           => $scraped['notes'] ?? null,
                        ]));

                        $extracted = collect($scraped)->except(['purchase_url', 'source', 'notes'])->filter()->count();

                        if ($extracted > 2) {
                            Notification::make()
                                ->title("Importado: {$item->brand} {$item->material} {$item->color}")
                                ->body("Se extrajeron {$extracted} datos. Revisa y ajusta si es necesario.")
                                ->success()->send();
                        } else {
                            Notification::make()
                                ->title('Importado con datos parciales')
                                ->body('No se pudieron extraer todos los datos. Completa manualmente.')
                                ->warning()->send();
                        }
                    }),
            ])
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
