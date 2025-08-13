<?php

namespace App\Observers;

use App\Models\Purchase;

class PurchaseObserver
{
    public function saved(Purchase $purchase)
    {
        // Hitung total_price berdasarkan purchaseItems
        $purchase->total_price = $purchase->purchaseItems()->sum('total_price');

        $purchase->amount_due = max(0, $purchase->total_price - $purchase->amount_paid);

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
