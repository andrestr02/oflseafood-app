<?php

namespace App\Observers;

use App\Models\PurchaseItem;
use App\Models\ProductItem;
use Illuminate\Support\Facades\DB;

class PurchaseItemObserver
{
    public function created(PurchaseItem $item)
    {
        DB::transaction(function () use ($item) {
            // 1. Buat ProductItem
            $productItems = [];

            if ($item->qty_unit && $item->qty_unit > 0) {
                $avgWeight = $item->weight_kg / $item->qty_unit;
                for ($i = 0; $i < $item->qty_unit; $i++) {
                    $productItems[] = ProductItem::create([
                        'product_id' => $item->product_id,
                        'purchase_item_id' => $item->id,
                        'weight_kg' => round($avgWeight, 3),
                        'price_sale' => round($avgWeight * $item->product->price_per_kg, 2),
                        'status' => 'available',
                        'added_at' => now(),
                    ]);
                }
            } else {
                $productItems[] = ProductItem::create([
                    'product_id' => $item->product_id,
                    'purchase_item_id' => $item->id,
                    'weight_kg' => $item->weight_kg,
                    'price_sale' => $item->weight_kg * $item->product->price_per_kg,
                    'status' => 'available',
                    'added_at' => now(),
                ]);
            }

            // 2. Update ProductVariant & stok/HPP
            foreach ($item->productVariants as $variantInput) {
                // pilih ProductItem yang sesuai (misal pertama, atau berdasarkan logika berat)
                $productItem = $productItems[0];

                $variant = $variantInput; // variantInput dari form
                $variant->update([
                    'product_item_id' => $productItem->id,
                    'stock' => $variant->weight_kg,
                    'hpp_per_kg' => $item->price_per_kg,
                ]);
            }
        });
    }
}
