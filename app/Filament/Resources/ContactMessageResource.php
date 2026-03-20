<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Mensajes de Contacto';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->label('Nombre')->disabled(),
            TextInput::make('email')->label('Email')->disabled(),
            TextInput::make('service')->label('Servicio de interés')->disabled(),
            Textarea::make('message')->label('Mensaje')->rows(5)->disabled()->columnSpanFull(),
            Toggle::make('read')->label('Marcado como leído'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('read')->label('')->boolean()
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-o-envelope')
                    ->trueColor('gray')
                    ->falseColor('warning'),
                TextColumn::make('name')->label('Nombre')->searchable(),
                TextColumn::make('email')->label('Email')->searchable()->copyable(),
                TextColumn::make('service')->label('Servicio')->badge()->default('—'),
                TextColumn::make('message')->label('Mensaje')->limit(60)->wrap(),
                TextColumn::make('created_at')->label('Recibido')->since()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('read')->label('Estado')
                    ->trueLabel('Leídos')
                    ->falseLabel('No leídos')
                    ->placeholder('Todos'),
            ])
            ->actions([
                Action::make('mark_read')
                    ->label('Marcar leído')
                    ->icon('heroicon-o-check')
                    ->color('gray')
                    ->visible(fn (ContactMessage $r) => ! $r->read)
                    ->action(function (ContactMessage $record) {
                        $record->update(['read' => true]);
                        Notification::make()->title('Marcado como leído')->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view'  => Pages\ViewContactMessage::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) ContactMessage::unread()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
