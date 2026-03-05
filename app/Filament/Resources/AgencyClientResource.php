<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgencyClientResource\Pages;
use App\Filament\Resources\AgencyClientResource\RelationManagers;
use App\Models\AgencyClient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgencyClientResource extends Resource
{
    protected static ?string $model = AgencyClient::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    
    protected static ?string $navigationLabel = 'Directorio (Agencia)';
    
    protected static ?string $modelLabel = 'Cliente Corporativo';
    
    protected static ?string $pluralModelLabel = 'Clientes Corporativos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos Corporativos')
                    ->description('Información principal de la empresa contratante.')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Compañía / Marca')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('contact_name')
                            ->label('Nombre del Contacto')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Canales de Comunicación y Estatus')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono (WhatsApp)')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->label('Fase del Pipeline')
                            ->options([
                                'lead' => 'Lead (Prospecto Inicial)',
                                'active_client' => 'Cliente Activo (Proyecto en Curso)',
                                'archived' => 'Archivado (Proyecto Finalizado)',
                            ])
                            ->default('lead')
                            ->required(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('contact_name')
                    ->label('Contacto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Pipeline Estatus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'lead' => 'warning',
                        'active_client' => 'success',
                        'archived' => 'gray',
                        default => 'primary',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Generado el')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filtrar Fase')
                    ->options([
                        'lead' => 'Solo Prospectos',
                        'active_client' => 'Solo Activos',
                    ]),
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
            'index' => Pages\ListAgencyClients::route('/'),
            'create' => Pages\CreateAgencyClient::route('/create'),
            'edit' => Pages\EditAgencyClient::route('/{record}/edit'),
        ];
    }
}
