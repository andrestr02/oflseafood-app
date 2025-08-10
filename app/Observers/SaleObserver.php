<?php

namespace App\Observers;

use App\Models\Sale;

class SaleObserver
{
    public function created(Sale $sale)
    {
        $sale->total_price = $sale->items()->sum('price_sold');
        $sale->amount_due = max(0, $sale->total_price - $sale->amount_paid);
        $sale->payment_status = $sale->amount_due <= 0 ? 'Lunas' : 'Belum Lunas';
        $sale->saveQuietly();
    }

    public function saved(Sale $sale)
    {
        $sale->total_price = $sale->items()->sum('price_sold');
        $sale->amount_due = max(0, $sale->total_price - $sale->amount_paid);
        $sale->payment_status = $sale->amount_due <= 0 ? 'Lunas' : 'Belum Lunas';
        $sale->saveQuietly();
    }
}
