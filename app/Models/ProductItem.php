<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class ProductItem extends Model
{
    protected $fillable = ['product_id', 'weight_kg', 'price_total', 'status'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
