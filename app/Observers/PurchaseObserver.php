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
        $purchase->total_price = $purchase->items()->sum('total_price');
        $purchase->amount_due = $purchase->total_price - $purchase->amount_paid;
        $purchase->payment_status = $purchase->amount_due <= 0 ? 'Lunas' : 'Belum Lunas';
        $purchase->saveQuietly();
    }

    public function saved(Purchase $purchase)
    {
        // ensure totals consistent
        $purchase->total_price = $purchase->items()->sum('total_price');
        $purchase->amount_due = max(0, $purchase->total_price - $purchase->amount_paid);
        $purchase->payment_status = $purchase->amount_due <= 0 ? 'Lunas' : 'Belum Lunas';
        $purchase->saveQuietly();
    }
}
