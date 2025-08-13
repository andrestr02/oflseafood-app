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
use Filament\Tables\Columns\TextColumn;

class PurchaseItemResource extends Resource
{
    protected static ?string $model = PurchaseItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';



    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('purchase_id')
                ->label('Purchase (Nota)')
                ->relationship('purchase', 'invoice_number')
                ->required(),

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
                ->disabled(),


            Repeater::make('productVariants')
                ->label('Varian Produk')
                ->relationship('productVariants')
                ->schema([
                    Select::make('id')
                        ->label('Varian')
                        ->relationship('variant', 'name') // pastikan relasi variant ada di model ProductVariant
                        ->required(),

                    TextInput::make('weight')
                        ->label('Berat Varian (kg)')
                        ->numeric()
                        ->required(),

                    TextInput::make('price_sale')
                        ->label('Harga Jual')
                        ->numeric()
                        ->required(),
                ])
                ->columns(3)
                ->addActionLabel('Tambah Varian'),
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
