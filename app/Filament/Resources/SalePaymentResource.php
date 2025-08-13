<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalePaymentResource\Pages;
use App\Filament\Resources\SalePaymentResource\RelationManagers;
use App\Models\SalePayment;
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

class SalePaymentResource extends Resource
{
    protected static ?string $model = SalePayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('sale_id')
                    ->relationship('sale', 'sale_number')
                    ->searchable()
                    ->required(),
                DatePicker::make('payment_date')->required()->default(now()),
                TextInput::make('amount')->numeric()->required(),
                TextInput::make('method')->maxLength(50),
                Textarea::make('notes')->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sale.sale_number')->label('No. Penjualan')->sortable(),
                TextColumn::make('payment_date')->date()->sortable(),
                TextColumn::make('amount')->money('IDR')->sortable(),
                TextColumn::make('method'),
                TextColumn::make('notes')->limit(50),
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
            'index' => Pages\ListSalePayments::route('/'),
            'create' => Pages\CreateSalePayment::route('/create'),
            'edit' => Pages\EditSalePayment::route('/{record}/edit'),
        ];
    }
}
