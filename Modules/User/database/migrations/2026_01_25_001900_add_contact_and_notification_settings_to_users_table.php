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
        Schema::table('users', function (Blueprint $table) {
            
            // Social media contact fields
            $table->boolean('whatsapp_enabled')->default(false);
            $table->string('whatsapp_number')->nullable();

            $table->boolean('call_enabled')->default(false);
            $table->string('call_number')->nullable();

            // Notification settings
            $table->boolean('notify_ad_status')->default(true);        // حالة الإعلان
            $table->boolean('notify_messages')->default(true);          // رسائل / تواصل
            $table->boolean('notify_ad_expiry')->default(true);         // انتهاء الإعلان
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_enabled',
                'whatsapp_number',
                'call_enabled',
                'call_number',
                'notify_ad_status',
                'notify_messages',
                'notify_ad_expiry',
            ]);
        });
    }
};
