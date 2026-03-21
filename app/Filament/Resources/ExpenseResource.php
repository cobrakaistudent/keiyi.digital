<?php

namespace App\Filament\Resources;

use App\Models\BusinessCost;
use App\Models\Expense;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationLabel = 'Registro de Gastos';
    protected static ?string $navigationGroup = 'Economía';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(3)->schema([
                DatePicker::make('date')->label('Fecha')->required()->default(now()),
                Select::make('category')->label('Categoría')->required()->options([
                    'hosting'     => 'Hosting / Dominio',
                    'ai_tools'    => 'Herramientas IA',
                    'filament'    => 'Filamento / Material 3D',
                    'electricity' => 'Electricidad',
                    'equipment'   => 'Equipo / Hardware',
                    'software'    => 'Software / Licencias',
                    'marketing'   => 'Marketing / Publicidad',
                    'development' => 'Desarrollo',
                    'legal'       => 'Legal / Fiscal',
                    'other'       => 'Otro',
                ]),
                TextInput::make('vendor')->label('Proveedor')->placeholder('Amazon, Bambu Lab, CFE...'),
            ]),
            TextInput::make('description')->label('Descripción')->required()->columnSpanFull()
                ->placeholder('Spool PLA negro eSUN, Mensualidad Claude Pro, etc.'),
            Grid::make(3)->schema([
                TextInput::make('amount')->label('Monto')->numeric()->required()->prefix('$'),
                Select::make('currency')->label('Moneda')->options([
                    'MXN' => 'MXN', 'USD' => 'USD',
                ])->default('MXN')->reactive(),
                Select::make('payment_method')->label('Método de pago')->options([
                    'transfer' => 'Transferencia',
                    'card'     => 'Tarjeta',
                    'cash'     => 'Efectivo',
                    'paypal'   => 'PayPal',
                    'other'    => 'Otro',
                ]),
            ]),
            TextInput::make('receipt_url')->label('Link a comprobante (opcional)')->url()->columnSpanFull(),
            Textarea::make('notes')->label('Notas')->rows(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('category')->label('Categoría')
                    ->getTitleFromRecordUsing(fn (Expense $r) => match($r->category) {
                        'hosting'     => 'Hosting / Dominio',
                        'ai_tools'    => 'Herramientas IA',
                        'filament'    => 'Filamento / Material 3D',
                        'electricity' => 'Electricidad',
                        'equipment'   => 'Equipo / Hardware',
                        'software'    => 'Software / Licencias',
                        'marketing'   => 'Marketing / Publicidad',
                        'development' => 'Desarrollo',
                        'legal'       => 'Legal / Fiscal',
                        default       => 'Otro',
                    }),
            ])
            ->columns([
                TextColumn::make('date')->label('Fecha')->date('d M Y')->sortable(),
                TextColumn::make('description')->label('Descripción')->searchable()->limit(40),
                TextColumn::make('category')->label('Categoría')->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'hosting'     => 'Hosting',
                        'ai_tools'    => 'IA',
                        'filament'    => 'Filamento',
                        'electricity' => 'Luz',
                        'equipment'   => 'Equipo',
                        'software'    => 'Software',
                        'marketing'   => 'Marketing',
                        'development' => 'Dev',
                        'legal'       => 'Legal',
                        default       => 'Otro',
                    })
                    ->color(fn ($state) => match($state) {
                        'ai_tools'    => 'info',
                        'equipment'   => 'warning',
                        'filament'    => 'success',
                        'electricity' => 'danger',
                        default       => 'gray',
                    }),
                TextColumn::make('vendor')->label('Proveedor')->limit(20),
                TextColumn::make('amount')->label('Monto')
                    ->formatStateUsing(fn ($state, $record) => '$' . number_format($state, 2) . ' ' . $record->currency),
                TextColumn::make('amount_mxn')->label('MXN')
                    ->money('MXN')
                    ->summarize(Sum::make()->label('Total')->money('MXN'))
                    ->color('danger'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('category')->label('Categoría')->options([
                    'hosting'     => 'Hosting',
                    'ai_tools'    => 'Herramientas IA',
                    'filament'    => 'Filamento',
                    'electricity' => 'Electricidad',
                    'equipment'   => 'Equipo',
                    'software'    => 'Software',
                    'marketing'   => 'Marketing',
                    'development' => 'Desarrollo',
                    'legal'       => 'Legal',
                    'other'       => 'Otro',
                ]),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Resources\ExpenseResource\Pages\ListExpenses::route('/'),
            'create' => \App\Filament\Resources\ExpenseResource\Pages\CreateExpense::route('/create'),
            'edit'   => \App\Filament\Resources\ExpenseResource\Pages\EditExpense::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $total = Expense::grandTotal();
        return $total > 0 ? '$' . number_format($total, 0) : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
