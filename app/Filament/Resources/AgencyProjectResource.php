<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgencyProjectResource\Pages;
use App\Models\AgencyProject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AgencyProjectResource extends Resource
{
    protected static ?string $model = AgencyProject::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    
    protected static ?string $navigationLabel = 'Gestión de Proyectos';
    
    protected static ?string $modelLabel = 'Proyecto';
    
    protected static ?string $pluralModelLabel = 'Proyectos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identificación del Proyecto')
                    ->description('Asigna el proyecto a un cliente y define el objetivo.')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label('Cliente / Marca')
                            ->relationship('client', 'company_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('title')
                            ->label('Título del Proyecto')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Detalles y Tiempos')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción / Alcance')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('deadline')
                            ->label('Fecha de Entrega (Deadline)')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\Select::make('status')
                            ->label('Estatus Actual')
                            ->options([
                                'briefing' => 'Briefing (Definiendo)',
                                'in_progress' => 'En Progreso (Producción)',
                                'delivered' => 'Entregado (Finalizado)',
                            ])
                            ->default('briefing')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.company_name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Proyecto')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'briefing' => 'gray',
                        'in_progress' => 'warning',
                        'delivered' => 'success',
                        default => 'primary',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Deadline')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->deadline < now() ? 'danger' : 'gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filtrar por Estado')
                    ->options([
                        'briefing' => 'Briefing',
                        'in_progress' => 'En Curso',
                        'delivered' => 'Entregados',
                    ]),
                Tables\Filters\SelectFilter::make('client_id')
                    ->label('Filtrar por Cliente')
                    ->relationship('client', 'company_name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAgencyProjects::route('/'),
        ];
    }
}
