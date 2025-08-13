<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductItemResource\Pages;
use App\Filament\Resources\ProductItemResource\RelationManagers;
use App\Models\ProductItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\BadgeColumn;




class ProductItemResource extends Resource
{
    protected static ?string $model = ProductItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_id')
                    ->label('Produk Induk')
                    ->relationship('product', 'name')
                    ->required(),

                Select::make('purchase_item_id')
                    ->label('Item Pembelian')
                    ->options(function () {
                        return \App\Models\PurchaseItem::with('product')->get()
                            ->mapWithKeys(fn($item) => [$item->id => $item->display_label]);
                    })
                    ->searchable()

                    ->nullable(),

                TextInput::make('weight_kg')
                    ->label('Berat (kg)')
                    ->numeric()
                    ->required(),

                TextInput::make('price_sale')
                    ->label('Harga Jual')
                    ->numeric()
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
                        'sold' => 'Terjual',
                        'reserved' => 'Dipesan',
                    ])
                    ->required(),

                DateTimePicker::make('added_at')
                    ->label('Tanggal Masuk')
                    ->default(now())
                    ->required(),

                DateTimePicker::make('sold_at')
                    ->label('Tanggal Terjual')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('product.name')->label('Produk')->sortable(),
                TextColumn::make('purchaseItem.display_label')->label('Item Pembelian'),
                TextColumn::make('weight_kg')->label('Berat (kg)'),
                TextColumn::make('price_sale')->label('Harga Jual')->money('idr', true),
                TextColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'primary' => 'Tersedia',
                        'success' => 'Terjual',
                        'warning' => 'Dipesan',
                    ])
                    ->colors([
                        'primary' => 'Tersedia',
                        'success' => 'Terjual',
                        'warning' => 'Dipesan',
                    ]),
                TextColumn::make('added_at')
                    ->label('Masuk')
                    ->dateTime(),

                TextColumn::make('sold_at')
                    ->label('Terjual')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListProductItems::route('/'),
            'create' => Pages\CreateProductItem::route('/create'),
            'edit' => Pages\EditProductItem::route('/{record}/edit'),
        ];
    }
}
