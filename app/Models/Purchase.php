<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    // Field yang boleh diisi mass assignment
    protected $fillable = [
        'supplier_id',
        'invoice_number',
        'date',
        'total_price',      // input manual
        'payment_status',   // otomatis
        'amount_paid',      // input manual
        'amount_due',       // otomatis
        'due_date',
        'notes'
    ];

    // Casting field ke tipe data yang tepat
    protected $casts = [
        'date' => 'datetime',
        'due_date' => 'datetime',
        'total_price' => 'integer',
        'amount_paid' => 'integer',
        'amount_due' => 'integer',
    ];

    // Relasi dengan Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relasi dengan PurchaseItem (jika nanti diperlukan)
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    // Relasi dengan pembayaran
    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    // Relasi ke Product lewat purchase_items (opsional)
    public function products()
    {
        return $this->belongsToMany(Product::class, 'purchase_items', 'purchase_id', 'product_id')
            ->withPivot('qty_unit', 'weight_kg', 'price_per_kg', 'total_price');
    }

    // Observer untuk invoice, sisa bayar, dan status pembayaran
    protected static function booted()
    {
        // Invoice otomatis
        static::creating(function ($purchase) {
            $datePart = now()->format('Ymd');
            $last = self::whereDate('created_at', now()->toDateString())
                ->latest('id')
                ->first();
            $number = $last ? ((int) substr($last->invoice_number, -4)) + 1 : 1;
            $purchase->invoice_number = 'INV-' . $datePart . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });

        // Hitung sisa bayar & status
        static::saving(function ($purchase) {
            // Pastikan total_price dan amount_paid adalah angka
            $total =  $purchase->total_price ?? 0;
            $paid  =  $purchase->amount_paid ?? 0;

            $purchase->amount_due = $total - $paid;

            if ($total > 0) {
                if ($paid >= $total) {
                    $purchase->payment_status = 'paid';       // Lunas
                } elseif ($paid > 0) {
                    $purchase->payment_status = 'partial';    // Sebagian Dibayar
                } else {
                    $purchase->payment_status = 'unpaid';     // Belum Dibayar
                }
            }
            // jika total=0, biarkan payment_status tetap dari input form
        });
    }
}
