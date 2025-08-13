<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;

use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;


class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pemasok';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Suplier')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Telepon Suplier')
                    ->tel()
                    ->maxLength(20),
                TextInput::make('email')
                    ->label('Email Suplier')
                    ->email()
                    ->maxLength(255),
                Textarea::make('address')
                    ->label('Alamat Suplier')
                    ->rows(3)
                    ->maxLength(65535),
                TextInput::make('notes'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('name')->label('Nama'),
                TextColumn::make('phone')->label('Telepon'),
                TextColumn::make('email')->label('Email')->limit(30),
                TextColumn::make('address')->label('Alamat')->limit(30),
                TextColumn::make('notes')->label('Catatan')->limit(30),
                TextColumn::make('created_at')->label('Dibuat')->dateTime(),
                TextColumn::make('updated_at')->label('Diperbarui')->dateTime(),
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
