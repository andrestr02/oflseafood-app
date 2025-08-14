<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseItemResource\Pages;
use App\Models\PurchaseItem;
use App\Models\Variant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PurchaseItemResource extends Resource
{
    protected static ?string $model = PurchaseItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Pilih nota/purchase
            Select::make('purchase_id')
                ->label('Purchase (Nota)')
                ->relationship('purchase', 'invoice_number')
                ->required()
                ->reactive(), // supaya select produk bisa update ketika nota dipilih

            // Pilih produk (hanya produk yang ada di nota)
            Select::make('product_id')
                ->label('Produk')
                ->required()
                ->reactive()
                ->options(function (callable $get) {
                    $purchaseId = $get('purchase_id');
                    if (!$purchaseId) {
                        return [];
                    }

                    // Ambil semua product_id dari purchase_item yang terkait nota ini beserta nama produk
                    return \App\Models\PurchaseItem::where('purchase_id', $purchaseId)
                        ->with('product') // pastikan relasi 'product' ada di model PurchaseItem
                        ->get()
                        ->pluck('product.name', 'product_id')
                        ->toArray();
                }),


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

            // Placeholder untuk peringatan selisih berat
            Placeholder::make('weight_warning')
                ->label('Peringatan Selisih Berat')
                ->content(function ($get) {
                    $purchaseWeight = (float) ($get('weight_kg') ?? 0);
                    $items = $get('product_items') ?? [];
                    $itemsArray = is_array($items) ? $items : $items->toArray();
                    $totalItemWeight = array_sum(array_map(fn($i) => (float) ($i['weight_kg'] ?? 0), $itemsArray));
                    $selisih = $purchaseWeight - $totalItemWeight;

                    if ($purchaseWeight == 0 || $totalItemWeight == 0) {
                        return 'Belum ada data untuk dihitung.';
                    }

                    if (abs($selisih) <= 0.2) {
                        return "✅ Selisih masih dalam batas toleransi (" . number_format($selisih, 2) . " kg).";
                    }

                    return "⚠️ Selisih berat: " . number_format($selisih, 2) . " kg.";
                }),

            // Repeater untuk input item per produk
            Repeater::make('product_items')
                ->label('Item Produk')
                ->relationship('productItems') // relasi di model PurchaseItem
                ->schema([
                    Hidden::make('product_id')
                        ->default(fn($get) => $get('product_id')),

                    Select::make('variant_id')
                        ->label('Varian')
                        ->required()
                        ->reactive()
                        ->options(function (callable $get) {
                            $productId = $get('../../product_id');
                            if (!$productId) return [];
                            return Variant::where('product_id', $productId)
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->afterStateUpdated(function ($state, callable $set) {
                            $variant = Variant::find($state);
                            $set('price_sale_per_kg', $variant?->price_sale_per_kg ?? 0);
                        }),

                    TextInput::make('weight_kg')
                        ->label('Berat (kg)')
                        ->numeric()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $set('total_price', $state * ($get('price_sale_per_kg') ?? 0));
                        }),

                    TextInput::make('price_sale_per_kg')
                        ->label('Harga Jual per kg')
                        ->numeric()
                        ->disabled(),

                    TextInput::make('total_price')
                        ->label('Total Harga')
                        ->numeric()
                        ->disabled(),
                ])
                ->columns(4)
                ->addActionLabel('Tambah Item'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->label('ID')->sortable(),
            TextColumn::make('purchase.invoice_number')->label('Nota')->sortable(),
            TextColumn::make('product.name')->label('Produk')->sortable(),
            TextColumn::make('productItems.weight_kg')->label('Berat (kg)'),
            TextColumn::make('productItems.total_price')->label('Total Harga')->money('idr', true),
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
