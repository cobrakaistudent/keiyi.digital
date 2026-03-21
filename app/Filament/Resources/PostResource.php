<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Blog';
    protected static ?string $navigationGroup = 'Contenido';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
            TextInput::make('slug')->required()->maxLength(255),
            Select::make('category')->options([
                'Marketing Digital' => 'Marketing Digital',
                'IA & Tendencias'   => 'IA & Tendencias',
                'Herramientas'      => 'Herramientas',
                'Estrategia'        => 'Estrategia',
                'Casos de Estudio'  => 'Casos de Estudio',
            ])->required(),
            Select::make('status')->options([
                'draft'     => 'Borrador',
                'pending'   => 'Pendiente de aprobacion',
                'approved'  => 'Aprobado',
                'published' => 'Publicado',
                'rejected'  => 'Rechazado',
            ])->required(),
            Textarea::make('excerpt')->required()->rows(3)->columnSpanFull(),
            Textarea::make('content')->required()->rows(20)->columnSpanFull(),
            TextInput::make('dominant_subreddit')->label('Subreddit fuente')->maxLength(100),
            Textarea::make('rejection_reason')->label('Motivo de rechazo')->rows(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Titulo')->searchable()->limit(50)->wrap(),
                TextColumn::make('category')->label('Categoria')->badge(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'approved'  => 'success',
                        'published' => 'info',
                        'rejected'  => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft'     => 'Borrador',
                        'pending'   => 'Pendiente',
                        'approved'  => 'Aprobado',
                        'published' => 'Publicado',
                        'rejected'  => 'Rechazado',
                        default     => $state,
                    }),
                TextColumn::make('word_count')->label('Palabras')->sortable(),
                TextColumn::make('dominant_subreddit')->label('Fuente')->badge(),
                TextColumn::make('created_at')->label('Recibido')->since()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->label('Estado')->options([
                    'pending'   => 'Pendiente',
                    'approved'  => 'Aprobado',
                    'published' => 'Publicado',
                    'rejected'  => 'Rechazado',
                ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Post $record) => in_array($record->status, ['pending', 'rejected']))
                    ->requiresConfirmation()
                    ->action(function (Post $record) {
                        $record->approve();
                        Notification::make()->title('Articulo aprobado')->success()->send();
                    }),

                Action::make('publish')
                    ->label('Publicar')
                    ->icon('heroicon-o-globe-alt')
                    ->color('info')
                    ->visible(fn (Post $record) => $record->status === 'approved')
                    ->requiresConfirmation()
                    ->action(function (Post $record) {
                        $record->publish();
                        Notification::make()->title('Articulo publicado')->success()->send();
                    }),

                Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Post $record) => in_array($record->status, ['pending', 'approved']))
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Motivo del rechazo')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Post $record, array $data) {
                        $record->reject($data['rejection_reason']);
                        Notification::make()->title('Articulo rechazado')->warning()->send();
                    }),

                EditAction::make()->label('Editar'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit'   => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Post::pending()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
