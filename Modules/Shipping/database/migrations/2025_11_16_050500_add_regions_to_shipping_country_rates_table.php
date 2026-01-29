<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_country_rates', function (Blueprint $table) {
            $table->unsignedBigInteger('shipping_state_id')->nullable()->after('shipping_country_id');
            $table->unsignedBigInteger('shipping_city_id')->nullable()->after('shipping_state_id');

            $table->foreign('shipping_state_id')
                ->references('id')
                ->on('shipping_states')
                ->nullOnDelete();

            $table->foreign('shipping_city_id')
                ->references('id')
                ->on('shipping_cities')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('shipping_country_rates', function (Blueprint $table) {
            $table->dropForeign(['shipping_state_id']);
            $table->dropForeign(['shipping_city_id']);
            $table->dropColumn(['shipping_state_id', 'shipping_city_id']);
        });
    }
};
