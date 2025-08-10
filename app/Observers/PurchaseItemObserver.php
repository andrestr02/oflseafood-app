<?php

namespace App\Observers;

use App\Models\PurchaseItem;
use App\Models\ProductItem;
use Illuminate\Support\Facades\DB;

class PurchaseItemObserver
{
    public function created(PurchaseItem $item)
    {
        // default behavior: if qty_unit provided, create product_items per ekor
        if ($item->qty_unit && $item->qty_unit > 0) {
            $avgWeight = $item->weight_kg / $item->qty_unit;
            for ($i = 0; $i < $item->qty_unit; $i++) {
                ProductItem::create([
                    'product_id' => $item->product_id,
                    'purchase_item_id' => $item->id,
                    'weight_kg' => round($avgWeight, 3),
                    'price_sale' => round($avgWeight * $item->product->price_per_kg, 2),
                    'status' => 'Tersedia',
                    'added_at' => now(),
                ]);
            }
        }
    }
}
