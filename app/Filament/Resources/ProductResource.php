<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Models\Purchase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name')
                    ->required(),

                TextInput::make('invoice_number')
                    ->label('No. Invoice')
                    ->required(),

                DatePicker::make('date')
                    ->label('Tanggal Pembelian')
                    ->default(now())
                    ->required(),

                TextInput::make('total_price')
                    ->label('Total Harga')
                    ->numeric()
                    ->required(),

                Select::make('payment_status')
                    ->label('Status Pembayaran')
                    ->options([
                        'unpaid' => 'Belum Dibayar',
                        'partial' => 'Sebagian Dibayar',
                        'paid' => 'Lunas',
                    ])
                    ->default('unpaid')
                    ->required(),

                TextInput::make('amount_paid')
                    ->label('Jumlah Dibayar')
                    ->numeric()
                    ->default(0),

                TextInput::make('amount_due')
                    ->label('Sisa Bayar')
                    ->numeric()
                    ->default(0),

                DatePicker::make('due_date')
                    ->label('Tanggal Jatuh Tempo')
                    ->nullable(),

                Textarea::make('notes')
                    ->label('Catatan')
                    ->nullable(),

                // Repeater untuk PurchaseItem
                Repeater::make('purchaseItems')
                    ->label('Daftar Barang')
                    ->relationship('purchaseItems')
                    ->columns(5)
                    ->addActionLabel('Tambah Barang')
                    ->schema([
                        Select::make('product_id')
                            ->label('Produk')
                            ->relationship('product', 'name')
                            ->required(),

                        TextInput::make('qty_unit')
                            ->label('Jumlah Unit')
                            ->numeric()
                            ->required(),

                        TextInput::make('weight_kg')
                            ->label('Berat (kg)')
                            ->numeric()
                            ->required(),

                        TextInput::make('price_per_kg')
                            ->label('Harga per kg')
                            ->numeric()
                            ->required(),

                        TextInput::make('total_price')
                            ->label('Total Harga')
                            ->numeric()
                            ->disabled()
                            ->dehydrateStateUsing(
                                fn($state, $component) => ($component->getParent()->getState()['weight_kg'] ?? 0) *
                                    ($component->getParent()->getState()['price_per_kg'] ?? 0)
                            ),

                        // Nested Repeater untuk ProductVariant
                        Repeater::make('productVariants')
                            ->label('Varian Produk')
                            ->relationship('productVariants')
                            ->columns(3)
                            ->addActionLabel('Tambah Varian')
                            ->schema([
                                Select::make('variant_id')
                                    ->label('Varian')
                                    ->relationship('variant', 'name')
                                    ->required(),

                                TextInput::make('weight_kg')
                                    ->label('Berat Varian')
                                    ->numeric()
                                    ->required(),

                                TextInput::make('price_sale')
                                    ->label('Harga Jual')
                                    ->numeric()
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('supplier.name')->label('Supplier')->sortable(),
                TextColumn::make('invoice_number')->label('No. Invoice')->sortable(),
                TextColumn::make('date')->label('Tanggal Pembelian')->date()->sortable(),
                TextColumn::make('total_price')->label('Total Harga')->money('idr', true),
                TextColumn::make('payment_status')->label('Status Pembayaran')->sortable(),
                TextColumn::make('amount_paid')->label('Jumlah Dibayar')->money('idr', true),
                TextColumn::make('amount_due')->label('Sisa Bayar')->money('idr', true),
                TextColumn::make('due_date')->label('Tanggal Jatuh Tempo')->date(),
                TextColumn::make('notes')->label('Catatan')->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }
}
