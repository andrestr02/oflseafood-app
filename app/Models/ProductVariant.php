<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;


class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'unit',
        'weight',
        'price_per_unit',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
