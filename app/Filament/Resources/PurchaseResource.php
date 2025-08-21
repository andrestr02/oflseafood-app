<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Models\Purchase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // Supplier
                Select::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name')
                    ->required(),

                // Nomor invoice otomatis
                Placeholder::make('invoice_number')
                    ->label('No. Invoice')
                    ->content(fn($get) => $get('invoice_number') ?? 'Sedang dibuat otomatis'),

                // Tanggal pembelian
                DatePicker::make('date')
                    ->label('Tanggal Pembelian')
                    ->default(now())
                    ->required(),

                // Total transaksi input manual
                TextInput::make('total_price')
                    ->label('Total Transaksi')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $amountPaid = $get('amount_paid') ?? 0;
                        $totalPrice = (int) $state;
                        if ($totalPrice <= 0) {
                            $set('payment_status', 'unpaid'); // Belum Dibayar
                            return;
                        } elseif ($amountPaid < $totalPrice) {
                            $set('payment_status', 'partial'); // Sebagian Dibayar
                        } else {
                            $set('payment_status', 'paid'); // Lunas
                        }
                        $set('amount_due', $totalPrice - $amountPaid); // Update sisa bayar otomatis
                    }),

                // Jumlah dibayar
                TextInput::make('amount_paid')
                    ->label('Jumlah Dibayar')
                    ->numeric()
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $totalPrice = (int) $get('total_price') ?? 0;
                        if ($state == 0) {
                            $set('payment_status', 'unpaid'); // Belum Dibayar
                        } elseif ($state < $totalPrice) {
                            $set('payment_status', 'partial'); // Sebagian Dibayar
                        } else {
                            $set('payment_status', 'paid'); // Lunas
                        }
                    }),

                // Sisa bayar otomatis (readonly)
                TextInput::make('amount_due')
                    ->label('Sisa Bayar')
                    ->numeric()
                    ->disabled()
                    ->default(0)
                    ->afterStateHydrated(fn($set, $get) => $set('amount_due', ($get('total_price') ?? 0) - ($get('amount_paid') ?? 0)))
                    ->afterStateUpdated(fn($set, $get) => $set('amount_due', ($get('total_price') ?? 0) - ($get('amount_paid') ?? 0))),

                // Status pembayaran
                Select::make('payment_status')
                    ->label('Status Pembayaran')
                    ->options([
                        'unpaid'  => 'Belum Dibayar',
                        'partial' => 'Sebagian Dibayar',
                        'paid'    => 'Lunas',
                    ])
                    ->default('unpaid')
                    ->required(),

                // Tanggal jatuh tempo
                DatePicker::make('due_date')
                    ->label('Tanggal Jatuh Tempo')
                    ->nullable(),

                // Catatan tambahan
                Textarea::make('notes')
                    ->label('Catatan')
                    ->nullable(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //nomor urut
                Tables\Columns\TextColumn::make('id')
                    ->label('No.')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state) => '#' . $state)
                    ->size('sm'),

                Tables\Columns\TextColumn::make('supplier.name')->label('Supplier')
                    ->sortable()
                    ->searchable()
                    ->size('lg'),
                Tables\Columns\TextColumn::make('invoice_number')->label('No. Invoice')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')->label('Tanggal')->date()->sortable(),
                Tables\Columns\TextColumn::make('total_price')->label('Nilai Transaksi')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->searchable()
                    ->size('lg'),
                Tables\Columns\TextColumn::make('amount_paid')->label('Jumlah Dibayar')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('amount_due')->label('Sisa Bayar')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('due_date')->label('Jatuh Tempo')->date()->sortable(),
                Tables\Columns\TextColumn::make('notes')->label('Catatan')->limit(50)->sortable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Status Pembayaran')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'unpaid'  => 'Belum Dibayar',
                        'partial' => 'Sebagian Dibayar',
                        'paid'    => 'Lunas',
                        default   => '-',
                    })
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'paid'    => 'success',
                        'partial' => 'warning',
                        'unpaid'  => 'danger',
                        default   => 'gray',
                    }),


            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit'   => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }
}
