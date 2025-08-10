<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'product_id', 'qty_unit', 'weight_kg', 'price_per_kg', 'total_price'];

    protected $casts = ['weight_kg' => 'decimal:3', 'price_per_kg' => 'decimal:2', 'total_price' => 'decimal:2'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function createdProductItems()
    {
        return $this->hasMany(ProductItem::class);
    }
}
