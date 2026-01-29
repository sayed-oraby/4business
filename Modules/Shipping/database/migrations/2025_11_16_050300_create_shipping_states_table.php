<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_states', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipping_country_id');
            $table->string('code', 10);
            $table->string('name_en');
            $table->string('name_ar');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->timestamps();

            $table->foreign('shipping_country_id')
                ->references('id')
                ->on('shipping_countries')
                ->onDelete('cascade');

            $table->unique(['shipping_country_id', 'code'], 'shipping_states_country_code_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_states');
    }
};
