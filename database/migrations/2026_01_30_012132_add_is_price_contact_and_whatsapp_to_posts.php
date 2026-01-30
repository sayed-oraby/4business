<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'is_price_contact')) {
                $table->boolean('is_price_contact')->default(false)->after('price');
            }
            if (!Schema::hasColumn('posts', 'whatsapp_number')) {
                $table->string('whatsapp_number')->nullable()->after('mobile_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'is_price_contact')) {
                $table->dropColumn('is_price_contact');
            }
            if (Schema::hasColumn('posts', 'whatsapp_number')) {
                $table->dropColumn('whatsapp_number');
            }
        });
    }
};
