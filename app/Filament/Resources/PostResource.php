<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
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
            TextInput::make('title')->label('Título')->required()->maxLength(255)->columnSpanFull(),
            TextInput::make('slug')->required()->maxLength(255),
            Select::make('category')->label('Categoría')->options([
                'Marketing Digital' => 'Marketing Digital',
                'IA & Tendencias'   => 'IA & Tendencias',
                'Herramientas'      => 'Herramientas',
                'Estrategia'        => 'Estrategia',
                'Casos de Estudio'  => 'Casos de Estudio',
            ])->required(),
            Select::make('status')->label('Estado')->options([
                'draft'     => 'Borrador',
                'pending'   => 'En revisión',
                'approved'  => 'Aprobado',
                'published' => 'Publicado',
                'rejected'  => 'Necesita correcciones',
            ])->required(),
            Textarea::make('excerpt')->label('Extracto')->required()->rows(3)->columnSpanFull(),
            RichEditor::make('content')->label('Contenido')->required()->columnSpanFull()
                ->toolbarButtons([
                    'bold', 'italic', 'underline', 'strike',
                    'h2', 'h3',
                    'bulletList', 'orderedList',
                    'link', 'blockquote',
                    'undo', 'redo',
                ]),
            Textarea::make('rejection_reason')->label('Notas de corrección')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Título')->searchable()->limit(45)->wrap(),
                TextColumn::make('category')->label('Categoría')->badge(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'     => 'gray',
                        'pending'   => 'warning',
                        'approved'  => 'success',
                        'published' => 'info',
                        'rejected'  => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft'     => 'Borrador',
                        'pending'   => 'En revisión',
                        'approved'  => 'Aprobado',
                        'published' => 'Publicado',
                        'rejected'  => 'Correcciones',
                        default     => $state,
                    }),
                TextColumn::make('word_count')->label('Palabras')->sortable(),
                TextColumn::make('editorial_comments')
                    ->label('Comentarios')
                    ->getStateUsing(fn (Post $r) => count($r->editorial_comments ?? []))
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray'),
                TextColumn::make('source_file')->label('Origen')->limit(25)->color('gray'),
                TextColumn::make('created_at')->label('Recibido')->since()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->label('Estado')->options([
                    'draft'     => 'Borrador',
                    'pending'   => 'En revisión',
                    'approved'  => 'Aprobado',
                    'published' => 'Publicado',
                    'rejected'  => 'Correcciones',
                ]),
            ])
            ->actions([
                // Comentar — agregar feedback editorial
                Action::make('comment')
                    ->label('Comentar')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('warning')
                    ->visible(fn (Post $r) => in_array($r->status, ['draft', 'pending', 'rejected']))
                    ->form([
                        Select::make('type')->label('Tipo')->options([
                            'correction' => 'Corrección (hay que cambiar algo)',
                            'suggestion' => 'Sugerencia (mejoraría con...)',
                            'approval'   => 'Aprobación (esto está bien)',
                        ])->required()->default('correction'),
                        Textarea::make('text')->label('Comentario')->required()->rows(4)
                            ->placeholder('Ej: El título no engancha, cambiar por algo más específico...'),
                    ])
                    ->action(function (Post $record, array $data) {
                        $record->addComment($data['text'], $data['type']);
                        $record->update(['status' => 'pending']);
                        Notification::make()->title('Comentario agregado')->success()->send();
                    }),

                // Enviar a correcciones
                Action::make('request_corrections')
                    ->label('Pedir correcciones')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->visible(fn (Post $r) => in_array($r->status, ['draft', 'pending']) && count($r->editorial_comments ?? []) > 0)
                    ->requiresConfirmation()
                    ->modalHeading('¿Enviar para correcciones?')
                    ->modalDescription('Los comentarios se mantendrán como guía para las correcciones.')
                    ->action(function (Post $record) {
                        $record->update(['status' => 'rejected']);
                        Notification::make()->title('Enviado para correcciones')->warning()->send();
                    }),

                // Aprobar
                Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Post $r) => in_array($r->status, ['draft', 'pending', 'rejected']))
                    ->requiresConfirmation()
                    ->action(function (Post $record) {
                        $record->approve();
                        Notification::make()->title('Artículo aprobado')->success()->send();
                    }),

                // Publicar
                Action::make('publish')
                    ->label('Publicar')
                    ->icon('heroicon-o-globe-alt')
                    ->color('info')
                    ->visible(fn (Post $r) => $r->status === 'approved')
                    ->requiresConfirmation()
                    ->modalHeading('¿Publicar este artículo?')
                    ->modalDescription('Será visible en keiyi.digital/blog para todos los visitantes.')
                    ->action(function (Post $record) {
                        $record->publish();
                        Notification::make()->title('¡Publicado en el blog!')->success()->send();
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
        $count = Post::needsReview()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
