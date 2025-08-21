<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition(): array
    {
        return [
            'supplier_id'    => Supplier::factory(), // bikin supplier otomatis
            'date'           => $this->faker->date(), // ✅ biar tidak null
            'total_price'    => 100000,
            'amount_paid'    => 0,
            'payment_status' => 'unpaid', // bisa: unpaid, partial, paid
            'amount_due'     => 100000,
            'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
        ];
    }
}
