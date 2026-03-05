<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Alumnos (Academia)';
    
    protected static ?string $modelLabel = 'Alumno';
    
    protected static ?string $pluralModelLabel = 'Alumnos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->description('Datos de contacto principales del alumno.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Autorización y Permisos')
                    ->description('Control de acceso a la Keiyi Academy.')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label('Rol del Sistema')
                            ->options([
                                'student' => 'Alumno (Student)',
                                'admin' => 'Administrador (Admin)',
                            ])
                            ->default('student')
                            ->required(),
                        Forms\Components\Select::make('approval_status')
                            ->label('Estatus de Aprobación')
                            ->options([
                                'pending' => 'Pendiente (En Revisión)',
                                'approved' => 'Aprobado (Acceso Total)',
                            ])
                            ->default('pending')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Correo copiado'),
                Tables\Columns\TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'student' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('approval_status')
                    ->label('Estatus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado El')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('approval_status')
                    ->label('Filtrar por Estatus')
                    ->options([
                        'pending' => 'Pendientes',
                        'approved' => 'Aprobados',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Aprobar Estudiante')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (User $record) => $record->update(['approval_status' => 'approved']))
                    ->hidden(fn (User $record) => $record->approval_status === 'approved'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
