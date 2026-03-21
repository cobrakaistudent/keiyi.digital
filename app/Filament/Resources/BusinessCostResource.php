<?php

namespace App\Filament\Resources;

use App\Models\BusinessCost;
use Filament\Forms\Components\Grid;
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
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class BusinessCostResource extends Resource
{
    protected static ?string $model = BusinessCost::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Costos Operativos';
    protected static ?string $navigationGroup = 'Economía';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                TextInput::make('name')->label('Nombre')->required()->placeholder('Hostinger, Claude Pro, Luz CFE...'),
                Select::make('category')->label('Categoría')->required()->options([
                    'hosting'     => 'Hosting / Dominio',
                    'ai_tools'    => 'Herramientas IA (Claude, Gemini, etc.)',
                    'electricity' => 'Electricidad',
                    'filament'    => 'Filamento / Material 3D',
                    'software'    => 'Software / Licencias',
                    'development' => 'Desarrollo / Freelance',
                    'marketing'   => 'Marketing / Publicidad',
                    'legal'       => 'Legal / Fiscal',
                    'other'       => 'Otro',
                ]),
            ]),
            Grid::make(3)->schema([
                TextInput::make('amount')->label('Monto')->numeric()->required()->prefix('$'),
                Select::make('currency')->label('Moneda')->options([
                    'MXN' => 'MXN', 'USD' => 'USD',
                ])->default('MXN'),
                Select::make('frequency')->label('Frecuencia')->required()->options([
                    'monthly'  => 'Mensual',
                    'yearly'   => 'Anual',
                    'one_time' => 'Único',
                    'per_kwh'  => 'Por kWh',
                    'per_unit' => 'Por unidad',
                ]),
            ]),
            TextInput::make('url')->label('Link de proveedor/compra')->url()->columnSpanFull(),
            Textarea::make('notes')->label('Notas')->rows(2)->columnSpanFull(),
            Toggle::make('active')->label('Activo')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('category')
                    ->label('Categoría')
                    ->getTitleFromRecordUsing(fn (BusinessCost $r) => match($r->category) {
                        'hosting'     => 'Hosting / Dominio',
                        'ai_tools'    => 'Herramientas IA',
                        'electricity' => 'Electricidad',
                        'filament'    => 'Filamento / Material',
                        'software'    => 'Software',
                        'development' => 'Desarrollo',
                        'marketing'   => 'Marketing',
                        'legal'       => 'Legal / Fiscal',
                        default       => 'Otro',
                    }),
            ])
            ->defaultGroup('category')
            ->columns([
                TextColumn::make('name')->label('Concepto')->searchable(),
                TextColumn::make('amount')->label('Monto')
                    ->formatStateUsing(fn ($state, $record) => '$' . number_format($state, 2) . ' ' . $record->currency),
                TextColumn::make('frequency')->label('Frecuencia')->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'monthly'  => 'Mensual',
                        'yearly'   => 'Anual',
                        'one_time' => 'Único',
                        'per_kwh'  => 'Por kWh',
                        'per_unit' => 'Por unidad',
                        default    => $state,
                    })
                    ->color(fn ($state) => match($state) {
                        'monthly'  => 'warning',
                        'yearly'   => 'info',
                        'one_time' => 'gray',
                        default    => 'gray',
                    }),
                TextColumn::make('monthly_cost')->label('Costo/Mes')
                    ->getStateUsing(fn ($record) => $record->monthly_cost)
                    ->money('MXN')
                    ->color('danger'),
                IconColumn::make('active')->label('Activo')->boolean(),
            ])
            ->defaultSort('category')
            ->actions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Resources\BusinessCostResource\Pages\ListBusinessCosts::route('/'),
            'create' => \App\Filament\Resources\BusinessCostResource\Pages\CreateBusinessCost::route('/create'),
            'edit'   => \App\Filament\Resources\BusinessCostResource\Pages\EditBusinessCost::route('/{record}/edit'),
        ];
    }
}
