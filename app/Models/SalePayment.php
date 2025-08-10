<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id', 'payment_date', 'amount', 'method', 'notes'];

    protected $casts = ['payment_date' => 'date'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
