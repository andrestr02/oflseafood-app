<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id', 'product_item_id', 'price_sold'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function productItem()
    {
        return $this->belongsTo(ProductItem::class);
    }
}
