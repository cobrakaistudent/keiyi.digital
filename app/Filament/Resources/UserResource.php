<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Usuarios';

    protected static ?string $modelLabel = 'Usuario';

    protected static ?string $pluralModelLabel = 'Usuarios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->schema([
                        Forms\Components\TextInput::make('name')->label('Nombre(s)')->required(),
                        Forms\Components\TextInput::make('apellido_paterno')->label('Apellido Paterno'),
                        Forms\Components\TextInput::make('apellido_materno')->label('Apellido Materno'),
                        Forms\Components\TextInput::make('email')->label('Email')->email()->unique(ignoreRecord: true)->required(),
                        Forms\Components\TextInput::make('password')->label('Contraseña')->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->rule(Rules\Password::defaults()),
                        Forms\Components\TextInput::make('phone')->label('Teléfono')->tel(),
                        Forms\Components\TextInput::make('company_name')->label('Empresa / Institución'),
                    ])->columns(2),

                Forms\Components\Section::make('Autorización y Rol')
                    ->schema([
                        Forms\Components\Select::make('approval_status')->label('Estatus')->options([
                            'pending' => 'Pendiente',
                            'approved' => 'Aprobado',
                            'rejected' => 'Rechazado',
                        ])->default('pending')->required(),
                        Forms\Components\Select::make('role')->label('Rol')->options([
                            'student' => 'Alumno',
                            'teacher' => 'Profesor',
                            'client' => 'Cliente',
                            'super-admin' => 'Admin',
                        ])->default('student')->required()->reactive(),
                        Forms\Components\Toggle::make('is_3d_client')->label('Cliente 3D'),
                    ])->columns(3),

                Forms\Components\Section::make('Configuración de Profesor')
                    ->schema([
                        Forms\Components\Select::make('student_limit')->label('Límite de alumnos')->options([
                            5 => '5 alumnos',
                            10 => '10 alumnos',
                            15 => '15 alumnos',
                            20 => '20 alumnos',
                            25 => '25 alumnos',
                        ]),
                        Forms\Components\Placeholder::make('students_count')
                            ->label('Alumnos inscritos')
                            ->content(fn (?User $record) => $record ? $record->students()->count() : 0),
                    ])->columns(2)
                    ->visible(fn (Forms\Get $get) => $get('role') === 'teacher'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')
                    ->formatStateUsing(fn ($state, $record) => $record->fullName())
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('approval_status')->label('Estatus')->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Pendiente', 'approved' => 'Aprobado', 'rejected' => 'Rechazado', default => $state,
                    }),
                Tables\Columns\TextColumn::make('role')->label('Rol')->badge()
                    ->color(fn (string $state) => match ($state) {
                        'super-admin' => 'danger',
                        'teacher' => 'warning',
                        'client' => 'info',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'super-admin' => 'Admin',
                        'teacher' => 'Profesor',
                        'client' => 'Cliente',
                        default => 'Alumno',
                    }),
                Tables\Columns\TextColumn::make('company_name')->label('Empresa')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('student_limit')->label('Límite')
                    ->formatStateUsing(fn ($state, $record) => $record->isTeacher() && $state
                        ? $record->students()->count().'/'.$state
                        : '—')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_3d_client')->label('3D')->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->label('Registro')
                    ->date('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('approval_status')->label('Estatus')->options([
                    'pending' => 'Pendientes', 'approved' => 'Aprobados', 'rejected' => 'Rechazados',
                ]),
                Tables\Filters\SelectFilter::make('role')->label('Rol')->options([
                    'student' => 'Alumno',
                    'teacher' => 'Profesor',
                    'client' => 'Cliente',
                    'super-admin' => 'Admin',
                ]),
            ])
            ->actions([
                Tables\Actions\Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (User $r) => $r->update(['approval_status' => 'approved']))
                    ->visible(fn (User $r) => $r->approval_status !== 'approved'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = User::where('approval_status', 'pending')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
