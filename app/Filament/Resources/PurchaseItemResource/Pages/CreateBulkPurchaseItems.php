<?php

namespace App\Filament\Resources\PurchaseItemResource\Pages;

use App\Filament\Resources\PurchaseItemResource;
use App\Models\PurchaseItem;
use Filament\Forms;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;

class CreateBulkPurchaseItems extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $resource = PurchaseItemResource::class;
    protected static string $view = 'filament.resources.purchase-item-resource.pages.create-bulk-purchase-items';
    protected static ?string $title = 'Tambah Banyak Purchase Item';

    public $purchaseId;
    public $items = [];

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('purchaseId')
                ->label('Purchase (Nota)')
                ->relationship('purchase', 'invoice_number')
                ->required(),

            Forms\Components\Repeater::make('items')
                ->label('Daftar Produk')
                ->schema([
                    Forms\Components\Select::make('product_id')
                        ->label('Produk')
                        ->relationship('product', 'name')
                        ->required(),

                    Forms\Components\TextInput::make('qty_unit')
                        ->label('Jumlah Unit (ekor)')
                        ->numeric()
                        ->required(),

                    Forms\Components\TextInput::make('weight_kg')
                        ->label('Berat (kg)')
                        ->numeric()
                        ->reactive()
                        ->required()
                        ->afterStateUpdated(fn($state, $set, $get) => $set('total_price', $state * ($get('price_per_kg') ?? 0))),

                    Forms\Components\TextInput::make('price_per_kg')
                        ->label('Harga per kg')
                        ->numeric()
                        ->reactive()
                        ->required()
                        ->afterStateUpdated(fn($state, $set, $get) => $set('total_price', $state * ($get('weight_kg') ?? 0))),

                    Forms\Components\TextInput::make('total_price')
                        ->label('Total Harga')
                        ->numeric()
                        ->disabled()
                        ->dehydrated(true),
                ])
                ->buttonLabel('Tambah Produk'),
        ];
    }

    public function create()
    {
        foreach ($this->items as $item) {
            $item['purchase_id'] = $this->purchaseId;
            PurchaseItem::create($item);
        }

        Notification::make()
            ->title('Berhasil menambahkan semua purchase item!')
            ->success()
            ->send();

        return redirect(PurchaseItemResource::getUrl('index'));
    }
}
