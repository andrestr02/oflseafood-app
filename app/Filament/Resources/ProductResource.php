<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                // Repeater untuk Product Variants
                Forms\Components\Repeater::make('variants')
                    ->label('Varian Produk')
                    ->relationship('variants') // relasi dari model Product
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Varian')
                            ->placeholder('Contoh: 1 kg up, 500 gr up')
                            ->required(),

                        Forms\Components\Select::make('unit')
                            ->label('Satuan')
                            ->options([
                                'kg' => 'Kilogram',
                                'ons' => 'Ons',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('weight')
                            ->label('Berat')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('price_per_unit')
                            ->label('Harga per Satuan')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                    ])
                    ->minItems(1)
                    ->addActionLabel('Tambah Varian')
                    ->columns(4)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Produk')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->label('Kategori')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('price_per_kg')->label('Harga per Kg')->money('IDR', true)->sortable(),
                Tables\Columns\TextColumn::make('stock_kg')->label('Stok (Kg)')->sortable(),
                Tables\Columns\TextColumn::make('description')->label('Deskripsi')->limit(30)->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
