<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('variants', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->after('id');

            $table->decimal('min_weight', 8, 2)
                ->nullable()
                ->after('description');

            $table->decimal('max_weight', 8, 2)
                ->nullable()
                ->after('min_weight');

            $table->decimal('price_sale_per_kg', 15, 2)
                ->nullable()
                ->after('max_weight');
        });
    }

    public function down(): void
    {
        Schema::table('variants', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'min_weight', 'max_weight', 'price_sale_per_kg']);
        });
    }
};
