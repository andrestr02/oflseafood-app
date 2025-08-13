<?php

namespace App\Observers;

use App\Models\Purchase;
use App\Models\ProductItem;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;

class PurchaseObserver
{
    public function created(Purchase $purchase)
    {
        // recalc totals when created if needed
        $purchase->total_price = (int) $purchase->items()->sum('total_price');
        $purchase->amount_due = $purchase->total_price - (int) $purchase->amount_paid;
        $purchase->payment_status = $purchase->amount_due <= 0 ? 'unpaid' : 'Belum Lunas';
        $purchase->saveQuietly();
    }

    public function saved(Purchase $purchase)
    {
        $purchase->total_price = (int) $purchase->purchaseItems()->sum('total_price');
        $purchase->amount_due = max(0, $purchase->total_price - (int) $purchase->amount_paid);

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
