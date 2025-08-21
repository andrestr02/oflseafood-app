<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantResource\Pages;
use App\Models\ProductVariant;
use App\Models\PurchaseItem;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([


                Select::make('product_id')
                    ->label('Produk')
                    ->relationship('product', 'name')
                    ->required(),

                TextInput::make('name')->label('Varian')->required(),
                TextInput::make('unit')->label('Unit')->required(),
                TextInput::make('weight')->label('Berat')->numeric()->required(),
                TextInput::make('price_per_unit')->label('Harga/unit')->numeric()->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('product.name')->label('Produk'),
                Tables\Columns\TextColumn::make('name')->label('Varian'),
                Tables\Columns\TextColumn::make('weight')->label('Berat'),
                Tables\Columns\TextColumn::make('price_per_unit')->label('Harga/unit')->money('idr', true),
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
            'index' => Pages\ListProductVariants::route('/'),
            'create' => Pages\CreateProductVariant::route('/create'),
            'edit' => Pages\EditProductVariant::route('/{record}/edit'),
        ];
    }
}
