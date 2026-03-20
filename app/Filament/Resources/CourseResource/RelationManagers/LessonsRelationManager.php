<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LessonsRelationManager extends RelationManager
{
    protected static string $relationship = 'lessons';
    protected static ?string $title = 'Lecciones';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')
                ->required()
                ->maxLength(255),
            TextInput::make('slug')
                ->required()
                ->maxLength(255),
            Select::make('type')
                ->options([
                    'lecture'     => 'Clase',
                    'quiz'        => 'Quiz',
                    'interactive' => 'Interactivo',
                ])
                ->required(),
            RichEditor::make('content_html')
                ->label('Contenido HTML')
                ->columnSpanFull(),
            TextInput::make('video_url')
                ->label('URL de video')
                ->url()
                ->maxLength(500),
            Textarea::make('video_outline')
                ->label('Outline del video')
                ->rows(3)
                ->columnSpanFull(),
            Textarea::make('quiz_data')
                ->label('Quiz Data (JSON)')
                ->rows(4)
                ->columnSpanFull(),
            Textarea::make('interactive_data')
                ->label('Interactive Data (JSON)')
                ->rows(4)
                ->columnSpanFull(),
            TextInput::make('sort_order')
                ->numeric()
                ->default(0),
            Toggle::make('is_published')
                ->label('Publicada'),
            TextInput::make('pass_threshold')
                ->numeric()
                ->minValue(0)
                ->maxValue(100),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Titulo')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'lecture'     => 'info',
                        'quiz'        => 'warning',
                        'interactive' => 'success',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'lecture'     => 'Clase',
                        'quiz'        => 'Quiz',
                        'interactive' => 'Interactivo',
                        default       => $state,
                    }),
                IconColumn::make('is_published')
                    ->label('Publicada')
                    ->boolean(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
