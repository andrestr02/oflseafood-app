<?php

namespace App\Observers;

use App\Models\PurchaseItem;

class PurchaseItemObserver
{
    public function created(PurchaseItem $purchaseItem)
    {
        $this->recalculatePurchase($purchaseItem);
    }

    public function updated(PurchaseItem $purchaseItem)
    {
        $this->recalculatePurchase($purchaseItem);
    }

    public function deleted(PurchaseItem $purchaseItem)
    {
        $this->recalculatePurchase($purchaseItem);
    }

    private function recalculatePurchase(PurchaseItem $purchaseItem)
    {
        $purchase = $purchaseItem->purchase;

        if ($purchase) {
            $purchase->total_price = (int) $purchase->purchaseItems()->sum('total_price');
            $purchase->amount_due = max(0, (int) $purchase->total_price - (int) $purchase->amount_paid);


            if ($purchase->amount_due <= 0) {
                $purchase->payment_status = 'paid';
            } elseif ($purchase->amount_paid > 0) {
                $purchase->payment_status = 'partial';
            } else {
                $purchase->payment_status = 'unpaid';
            }

            $purchase->saveQuietly();
        }
    }
}
