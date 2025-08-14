<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VariantSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('variants')->insert([
            [
                'product_id' => 1, // contoh ID produk "Ikan Putihan"
                'name' => 'Ukuran Bakaran',
                'description' => 'Berat antara 0,5kg - 2,5kg',
                'min_weight' => 0.5,
                'max_weight' => 2.5,
                'price_sale_per_kg' => 45000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 1, // contoh ID produk "Ikan Putihan"
                'name' => 'Ukuran Jumbo',
                'description' => 'Berat di atas 2,5kg',
                'min_weight' => 2.6,
                'max_weight' => null,
                'price_sale_per_kg' => 35000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 2, // contoh ID produk "Ikan Kakap"
                'name' => 'Ukuran Bakaran',
                'description' => 'Berat antara 0,5kg - 2,5kg',
                'min_weight' => 0.5,
                'max_weight' => 2.5,
                'price_sale_per_kg' => 65000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
