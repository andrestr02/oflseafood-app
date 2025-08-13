<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;


class ProductItem extends Model
{

    use HasFactory;

    protected $fillable = [
        'product_id',
        'purchase_item_id',
        'weight_kg',
        'price_sale',
        'status',
        'added_at',
        'sold_at',
    ];
    protected $casts = [
        'added_at' => 'datetime',
        'sold_at' => 'datetime',
        'weight_kg' => 'float',
        'price_sale' => 'float',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function purchaseItem()
    {
        return $this->belongsTo(PurchaseItem::class);
    }
}
