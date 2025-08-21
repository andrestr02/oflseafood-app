<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [


            'name' => 'Supplier ' . Str::random(5),
            'phone' => '08' . random_int(100000000, 999999999),
            'email' => 'supplier' . Str::random(5) . '@example.com',
            'address' => 'Jl.' . ucfirst(Str::random(10)) . ' No.' . random_int(1, 100),
            'notes' => '-',
        ];
    }
}
