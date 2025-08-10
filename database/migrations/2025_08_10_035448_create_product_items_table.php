<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_item_id')->nullable()->constrained('purchase_items')->nullOnDelete();
            $table->decimal('weight_kg', 8, 3);
            $table->decimal('price_sale', 14, 2);
            $table->enum('status', ['Tersedia', 'Terjual', 'Rusak', 'Dihapus'])->default('Tersedia');
            $table->timestamp('added_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_items');
    }
};
