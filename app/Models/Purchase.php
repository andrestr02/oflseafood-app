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
    public function products()
    {
        return $this->belongsToMany(Product::class, 'purchase_items', 'purchase_id', 'product_id')
            ->withPivot('qty_unit', 'weight_kg', 'price_per_kg', 'total_price');
    }
    protected static function booted()
    {
        static::creating(function ($purchase) {
            // Format: INV-YYYYMMDD-0001
            $datePart = now()->format('Ymd');

            // Ambil nomor terakhir untuk hari ini
            $last = self::whereDate('created_at', now()->toDateString())
                ->latest('id')
                ->first();

            $number = $last ? ((int) substr($last->invoice_number, -4)) + 1 : 1;

            $purchase->invoice_number = 'INV-' . $datePart . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
        static::saving(function ($purchase) {
            // Hitung total transaksi dari semua purchaseItems
            $total = $purchase->purchaseItems->sum(function ($item) {
                return $item->total_price ?? 0;
            });
            $purchase->total_price = $total;
        });
    }
}
