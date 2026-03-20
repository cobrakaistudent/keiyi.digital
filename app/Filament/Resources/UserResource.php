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
                        Forms\Components\Select::make('approval_status')
                            ->label('Estatus del Alumno')
                            ->options([
                                'pending'  => 'Pendiente de Revisión',
                                'approved' => 'Aprobado (Acceso Concedido)',
                                'rejected' => 'Rechazado (Acceso Denegado)',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Select::make('role')
                            ->label('Rol de Cuenta')
                            ->options([
                                'student'     => 'Alumno (Student)',
                                'super-admin' => 'Administrador (Super-Admin)',
                            ])
                            ->default('student')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Cliente 3D World')
                    ->description('Acceso al Taller de impresión 3D.')
                    ->schema([
                        Forms\Components\Toggle::make('is_3d_client')
                            ->label('Es cliente 3D')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $set('3d_client_approved_at', now()->toDateTimeString());
                                } else {
                                    $set('3d_client_approved_at', null);
                                }
                            }),
                        Forms\Components\DateTimePicker::make('3d_client_approved_at')
                            ->label('Aprobado como cliente 3D el')
                            ->disabled()
                            ->visible(fn (Forms\Get $get) => $get('is_3d_client')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre Completo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Correo copiado'),
                Tables\Columns\TextColumn::make('approval_status')
                    ->label('Estatus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super-admin' => 'danger',
                        'student' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_3d_client')
                    ->label('3D')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),
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
                Tables\Actions\Action::make('aprobar_estudiante')
                    ->label('Aprobar Alumno')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (User $record) => $record->update(['approval_status' => 'approved']))
                    ->hidden(fn (User $record) => $record->approval_status === 'approved'),
                Tables\Actions\Action::make('aprobar_3d')
                    ->label('Aprobar 3D')
                    ->icon('heroicon-o-cube')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(fn (User $record) => $record->update([
                        'is_3d_client'          => true,
                        '3d_client_approved_at' => now(),
                    ]))
                    ->hidden(fn (User $record) => $record->is_3d_client),
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
