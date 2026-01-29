<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_country_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_country_id')->constrained('shipping_countries')->cascadeOnDelete();
            $table->enum('calculation_type', ['flat', 'weight', 'order_total'])->default('flat');
            $table->decimal('base_price', 12, 3)->default(0);
            $table->decimal('price_per_kg', 12, 3)->nullable();
            $table->decimal('free_shipping_over', 12, 3)->nullable();
            $table->string('currency', 3)->default('KWD');
            $table->string('delivery_estimate_en')->nullable();
            $table->string('delivery_estimate_ar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_country_rates');
    }
};
