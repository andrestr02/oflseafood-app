<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'product_id', 'qty_unit', 'weight_kg', 'price_per_kg', 'total_price'];

    protected $casts = [
        'weight_kg' => 'integer',
        'price_per_kg' => 'integer',
        'total_price' => 'integer',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productItems()
    {
        return $this->hasMany(ProductItem::class);
    }
    public function getDisplayLabelAttribute()
    {
        return "Purchase #{$this->purchase_id} – {$this->weight_kg} kg " . $this->product->name;
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class, 'purchase_item_id');
    }
}
