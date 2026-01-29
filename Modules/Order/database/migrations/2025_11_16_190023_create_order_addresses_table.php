<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_address_id')->nullable()->constrained('user_addresses')->nullOnDelete();
            $table->string('type')->default('shipping'); // shipping, billing (for future use)
            $table->string('full_name');
            $table->string('phone');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('postal_code')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'type']);
            $table->index('country');
            $table->index('state');
            $table->index('city');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_addresses');
    }
};
