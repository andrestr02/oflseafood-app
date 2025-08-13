<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'invoice_number', 'date', 'total_price', 'payment_status', 'amount_paid', 'amount_due', 'due_date', 'notes'];

    private const DECIMAL = 'decimal:2';

    protected $casts = [
        'date' => 'datetime',
        'due_date' => 'datetime',
        'total_price' => self::DECIMAL,
        'amount_paid' => self::DECIMAL,
        'amount_due' => self::DECIMAL,
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class, 'purchase_item_id');
    }
    public function productItem()
    {
        return $this->belongsTo(ProductItem::class, 'purchase_item_id');
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }
}
