<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pastikan stok awal diisi 0 kalau kosong
        if (!isset($data['stock_kg']) || $data['stock_kg'] === null) {
            $data['stock_kg'] = 0;
        }
        return $data;
    }
}
