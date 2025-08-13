<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariant;


class Product extends Model
{
    protected $fillable = ['name', 'price_per_kg', 'description'];

    public function productItems()
    {
        return $this->hasMany(ProductItem::class);
    }
}

// app/Models/ProductItem.php
class ProductItem extends Model
{
    protected $fillable = ['product_id', 'weight_kg', 'price_total', 'status'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
