<?php


namespace App\Observers;

use App\Models\SaleItem;

class SaleItemObserver
{
    public function created(SaleItem $saleItem)
    {
        $productItem = $saleItem->productItem;
        if ($productItem && $productItem->status !== 'Terjual') {
            $productItem->update([
                'status' => 'Terjual',
                'sold_at' => now(),
            ]);
        }
    }
}
