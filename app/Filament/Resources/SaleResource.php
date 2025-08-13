<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;


use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('sale_number')->maxLength(50),
                    TextInput::make('customer_name')->required()->maxLength(255),
                    DatePicker::make('date')->required()->default(now()),
                    TextInput::make('amount_paid')->numeric()->default(0),
                    TextArea::make('notes')->maxLength(65535),

                ]),
                Repeater::make('items')
                    ->relationship('items')
                    ->schema([
                        Select::make('product_item_id')
                            ->relationship('productItem', 'id')
                            ->searchable()
                            ->required(),
                        TextInput::make('price_sold')->numeric()->required(),
                    ])
                    ->minItems(1)
                    ->addActionLabel('Tambah Item Penjualan'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sale_number')->sortable(),
                TextColumn::make('customer_name')->searchable()->sortable(),
                TextColumn::make('date')->date()->sortable(),
                TextColumn::make('total_price')->money('IDR')->sortable(),
                TextColumn::make('payment_status')->sortable(),
                TextColumn::make('amount_paid')->money('IDR'),
                TextColumn::make('amount_due')->money('IDR'),
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
