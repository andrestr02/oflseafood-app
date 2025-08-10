<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['sale_number','customer_name','date','total_price','payment_status','amount_paid','amount_due','due_date','notes'];

    protected $casts = ['date' => 'date','due_date' => 'date'];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }
}
