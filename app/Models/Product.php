<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariant;

use App\Models\Category;



class Product extends Model
{
    protected $fillable = ['name', 'price_per_kg', 'description'];

    public function productItems()
    {
        return $this->hasMany(ProductItem::class);
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function getTotalStockAttribute()
    {
        return $this->variants()->sum('stock');
    }
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
