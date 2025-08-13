<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchasePaymentResource\Pages;
use App\Filament\Resources\PurchasePaymentResource\RelationManagers;
use App\Models\PurchasePayment;
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

class PurchasePaymentResource extends Resource
{
    protected static ?string $model = PurchasePayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('purchase_id')
                    ->relationship('purchase', 'invoice_number')
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
                TextColumn::make('purchase.invoice_number')->label('Invoice')->sortable(),
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
            'index' => Pages\ListPurchasePayments::route('/'),
            'create' => Pages\CreatePurchasePayment::route('/create'),
            'edit' => Pages\EditPurchasePayment::route('/{record}/edit'),
        ];
    }
}
