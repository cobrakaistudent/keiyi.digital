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
    protected static ?string $navigationLabel = 'Alumnos (Academia)';
    protected static ?string $modelLabel = 'Alumno';
    protected static ?string $pluralModelLabel = 'Alumnos';

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
                    ])->columns(2),

                Forms\Components\Section::make('Autorización')
                    ->schema([
                        Forms\Components\Select::make('approval_status')->label('Estatus')->options([
                            'pending' => 'Pendiente', 'approved' => 'Aprobado', 'rejected' => 'Rechazado',
                        ])->default('pending')->required(),
                        Forms\Components\Select::make('role')->label('Rol')->options([
                            'student' => 'Alumno', 'instructor' => 'Instructor', 'super-admin' => 'Admin',
                        ])->default('student')->required(),
                        Forms\Components\Toggle::make('is_3d_client')->label('Cliente 3D'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')
                    ->formatStateUsing(fn ($state, $record) => $state . ' ' . ($record->apellido_paterno ?? ''))
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
                    ->color(fn (string $state) => match($state) {
                        'super-admin' => 'danger', 'instructor' => 'warning', default => 'info',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'super-admin' => 'Admin', 'instructor' => 'Instructor', default => 'Alumno',
                    }),
                Tables\Columns\TextColumn::make('enrollments_count')->label('Cursos')
                    ->counts('enrollments')->sortable()
                    ->badge()->color('info'),
                Tables\Columns\TextColumn::make('avg_progress')->label('Progreso')
                    ->getStateUsing(function ($record) {
                        $enrollments = $record->enrollments;
                        if ($enrollments->isEmpty()) return '—';
                        $avg = $enrollments->avg('progress_percent');
                        return round($avg) . '%';
                    })
                    ->color(fn ($state) => $state === '—' ? 'gray' : ($state === '100%' ? 'success' : 'warning')),
                Tables\Columns\IconColumn::make('is_3d_client')->label('3D')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Registro')
                    ->date('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('approval_status')->label('Estatus')->options([
                    'pending' => 'Pendientes', 'approved' => 'Aprobados', 'rejected' => 'Rechazados',
                ]),
                Tables\Filters\SelectFilter::make('role')->label('Rol')->options([
                    'student' => 'Alumno', 'super-admin' => 'Admin',
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
                Tables\Actions\Action::make('aprobar_3d')
                    ->label('Aprobar 3D')
                    ->icon('heroicon-o-cube')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(fn (User $r) => $r->update(['is_3d_client' => true, '3d_client_approved_at' => now()]))
                    ->visible(fn (User $r) => !$r->is_3d_client),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
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
        return (string) User::where('approval_status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
