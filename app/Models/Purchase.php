<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'invoice_number', 'date', 'total_price', 'payment_status', 'amount_paid', 'amount_due', 'due_date', 'notes'];



    protected $casts = [
        'date' => 'datetime',
        'due_date' => 'datetime',
        'total_price' => 'integer',
        'amount_paid' => 'integer',
        'amount_due' => 'integer',
    ];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
