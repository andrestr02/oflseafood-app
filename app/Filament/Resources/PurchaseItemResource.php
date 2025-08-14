<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseItemResource\Pages;
use App\Models\PurchaseItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;

class PurchaseItemResource extends Resource
{
    protected static ?string $model = PurchaseItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([

            // Pilih Purchase
            Select::make('purchase_id')
                ->label('Purchase (Nota)')
                ->relationship('purchase', 'invoice_number')
                ->required(),

            // Pilih Produk
            Select::make('product_id')
                ->label('Produk')
                ->relationship('product', 'name')
                ->required(),

            TextInput::make('qty_unit')
                ->label('Jumlah Unit (ekor)')
                ->numeric()
                ->required(),

            TextInput::make('weight_kg')
                ->label('Berat Total (kg)')
                ->numeric()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $set('total_price', $state * ($get('price_per_kg') ?? 0));
                }),

            TextInput::make('price_per_kg')
                ->label('Harga per kg')
                ->numeric()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $set('total_price', $state * ($get('weight_kg') ?? 0));
                }),

            TextInput::make('total_price')
                ->label('Total Harga')
                ->numeric()
                ->disabled()
                ->dehydrated(true),

            // Placeholder untuk warning
            Placeholder::make('weight_warning')
                ->label('Peringatan Selisih Berat')
                ->content(function ($get) {
                    $purchaseWeight = (float) $get('weight_kg') ?? 0;
                    $items = $get('product_items') ?? [];
                    $totalItemWeight = array_sum(array_map(fn($i) => (float) ($i['weight_kg'] ?? 0), $items));
                    $selisih = $purchaseWeight - $totalItemWeight;

                    if ($purchaseWeight == 0 || $totalItemWeight == 0) {
                        return 'Belum ada data untuk dihitung.';
                    }

                    // Toleransi 0.2 kg
                    if (abs($selisih) <= 0.2) {
                        return "✅ Selisih masih dalam batas toleransi (" . number_format($selisih, 2) . " kg).";
                    }

                    return "⚠️ Selisih berat: " . number_format($selisih, 2) . " kg.";
                }),

            // Repeater Product Item
            Repeater::make('product_items')
                ->label('Item Produk')
                ->relationship('productItems')
                ->schema([
                    Hidden::make('product_id')
                        ->default(fn($get) => $get('../../product_id')),

                    TextInput::make('weight_kg')
                        ->label('Berat (kg)')
                        ->numeric()
                        ->required(),

                    TextInput::make('price_sale')
                        ->label('Harga Jual')
                        ->numeric()
                        ->required(),
                ])
                ->columns(3)
                ->addActionLabel('Tambah Item'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->label('ID')->sortable(),
            TextColumn::make('purchase.invoice_number')->label('Nota')->sortable(),
            TextColumn::make('product.name')->label('Produk')->sortable(),
            TextColumn::make('weight_kg')->label('Berat (kg)')->sortable(),
            TextColumn::make('total_price')->label('Total Harga')->money('idr', true),
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
            'index' => Pages\ListPurchaseItems::route('/'),
            'create' => Pages\CreatePurchaseItem::route('/create'),
            'edit' => Pages\EditPurchaseItem::route('/{record}/edit'),
        ];
    }
}
