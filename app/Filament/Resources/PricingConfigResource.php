<?php

namespace App\Filament\Resources;

use App\Models\PricingConfig;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PricingConfigResource extends Resource
{
    protected static ?string $model = PricingConfig::class;
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Fórmula de Pricing';
    protected static ?string $navigationGroup = 'Economía';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('label')->label('Nombre')->disabled(),
            TextInput::make('value')->label('Valor')->required(),
            TextInput::make('unit')->label('Unidad')->disabled(),
            Textarea::make('description')->label('Descripción')->disabled()->rows(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')->label('Parámetro')->searchable(),
                TextColumn::make('value')->label('Valor')
                    ->formatStateUsing(fn ($state, $record) => $state . ($record->unit ? ' ' . $record->unit : '')),
                TextColumn::make('description')->label('Descripción')->limit(60)->color('gray'),
            ])
            ->actions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PricingConfigResource\Pages\ListPricingConfigs::route('/'),
            'edit'  => \App\Filament\Resources\PricingConfigResource\Pages\EditPricingConfig::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool { return false; }
}
