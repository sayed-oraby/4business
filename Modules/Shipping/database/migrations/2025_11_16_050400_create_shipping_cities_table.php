<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_cities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipping_state_id');
            $table->string('code', 20)->nullable();
            $table->string('name_en');
            $table->string('name_ar');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->timestamps();

            $table->foreign('shipping_state_id')
                ->references('id')
                ->on('shipping_states')
                ->onDelete('cascade');

            $table->unique(['shipping_state_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_cities');
    }
};
