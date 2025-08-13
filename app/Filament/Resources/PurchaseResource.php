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

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';




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
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }
}
