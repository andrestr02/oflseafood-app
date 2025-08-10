<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'payment_date', 'amount', 'method', 'notes'];

    protected $casts = ['payment_date' => 'date'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
