<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrintOrderResource\Pages;
use App\Models\PrintOrder;
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

class PrintOrderResource extends Resource
{
    protected static ?string $model = PrintOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationLabel = 'Órdenes';
    protected static ?string $navigationGroup = '3D World';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('status')->label('Estado')->options([
                'received'  => 'Recibida',
                'quoting'   => 'En cotización',
                'approved'  => 'Aprobada',
                'printing'  => 'Imprimiendo',
                'delivered' => 'Entregada',
                'cancelled' => 'Cancelada',
            ])->required(),
            TextInput::make('quoted_price')->label('Precio cotizado (MXN)')->numeric()->prefix('$'),
            TextInput::make('quoted_time')->label('Tiempo cotizado')->placeholder('Ej: 3-4 días'),
            Textarea::make('quote_details')->label('Detalles de cotización')->rows(4)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Cliente')->searchable(),
                TextColumn::make('type')->label('Tipo')->badge()
                    ->formatStateUsing(fn ($state) => $state === 'catalog' ? 'Catálogo' : 'Custom'),
                TextColumn::make('catalogItem.title')->label('Item')->limit(30)->default('—'),
                TextColumn::make('file_name')->label('Archivo')->limit(25)->default('—'),
                TextColumn::make('material')->label('Material'),
                TextColumn::make('quantity')->label('Cant.'),
                TextColumn::make('status')->label('Estado')->badge()
                    ->color(fn ($state) => match ($state) {
                        'received'  => 'warning',
                        'quoting'   => 'info',
                        'approved'  => 'success',
                        'printing'  => 'primary',
                        'delivered' => 'gray',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'received'  => 'Recibida',
                        'quoting'   => 'En cotización',
                        'approved'  => 'Aprobada',
                        'printing'  => 'Imprimiendo',
                        'delivered' => 'Entregada',
                        'cancelled' => 'Cancelada',
                    }),
                TextColumn::make('quoted_price')->label('Cotizado')->money('MXN')->default('—'),
                TextColumn::make('created_at')->label('Fecha')->since()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->label('Estado')->options([
                    'received'  => 'Recibida',
                    'quoting'   => 'En cotización',
                    'approved'  => 'Aprobada',
                    'printing'  => 'Imprimiendo',
                    'delivered' => 'Entregada',
                    'cancelled' => 'Cancelada',
                ]),
                SelectFilter::make('type')->label('Tipo')->options([
                    'catalog' => 'Catálogo',
                    'custom'  => 'Custom',
                ]),
            ])
            ->actions([
                Action::make('quote')
                    ->label('Cotizar')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('info')
                    ->visible(fn (PrintOrder $r) => in_array($r->status, ['received', 'quoting']))
                    ->form([
                        TextInput::make('quoted_price')->label('Precio (MXN)')->numeric()->required(),
                        TextInput::make('quoted_time')->label('Tiempo estimado')->required(),
                        Textarea::make('quote_details')->label('Detalles')->rows(3),
                    ])
                    ->action(function (PrintOrder $record, array $data) {
                        $record->update([
                            'status'        => 'quoting',
                            'quoted_price'  => $data['quoted_price'],
                            'quoted_time'   => $data['quoted_time'],
                            'quote_details' => $data['quote_details'] ?? null,
                        ]);
                        Notification::make()->title('Cotización enviada')->success()->send();
                    }),

                Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PrintOrder $r) => $r->status === 'quoting')
                    ->requiresConfirmation()
                    ->action(function (PrintOrder $record) {
                        $record->update(['status' => 'approved']);
                        Notification::make()->title('Orden aprobada')->success()->send();
                    }),

                Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (PrintOrder $r) => ! in_array($r->status, ['delivered', 'cancelled']))
                    ->requiresConfirmation()
                    ->action(function (PrintOrder $record) {
                        $record->update(['status' => 'cancelled']);
                        Notification::make()->title('Orden cancelada')->warning()->send();
                    }),

                EditAction::make()->label('Editar'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPrintOrders::route('/'),
            'edit'   => Pages\EditPrintOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) PrintOrder::pending()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
