<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScoutSourceResource\Pages;
use App\Filament\Resources\ScoutSourceResource\RelationManagers;
use App\Models\ScoutSource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScoutSourceResource extends Resource
{
    protected static ?string $model = ScoutSource::class;

    protected static ?string $navigationIcon = 'heroicon-o-signal';
    
    protected static ?string $navigationLabel = 'Fuentes RSS (Scout AI)';
    
    protected static ?string $modelLabel = 'Fuente de Inteligencia';
    
    protected static ?string $pluralModelLabel = 'Fuentes de Inteligencia';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles de la Fuente')
                    ->description('Configura los portales (Coursera, Udemy, Blogs) que el Scout AI vigilará.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Identificador')
                            ->placeholder('Ej: Blog de OpenAI')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('url')
                            ->label('URL (Endpoint / Feed RSS)')
                            ->url()
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración Técnica')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipo de Conexión')
                            ->options([
                                'rss' => 'RSS Feed Estándar',
                                'sitemap' => 'Sitemap XML',
                                'api' => 'API REST',
                            ])
                            ->default('rss')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Vigilancia Activa')
                            ->default(true)
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Fuente')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Protocolo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'rss' => 'warning',
                        'sitemap' => 'info',
                        'api' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->limit(40)
                    ->copyable()
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Activo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado de Vigilancia')
                    ->boolean()
                    ->trueLabel('Fuentes Activas')
                    ->falseLabel('Fuentes Inactivas'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScoutSources::route('/'),
            'create' => Pages\CreateScoutSource::route('/create'),
            'edit' => Pages\EditScoutSource::route('/{record}/edit'),
        ];
    }
}
