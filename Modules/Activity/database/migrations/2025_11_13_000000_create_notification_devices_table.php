<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('device_uuid')->unique();
            $table->string('device_token');
            $table->string('device_type', 20); // ios, android, web
            $table->string('app_version')->nullable();
            $table->string('language', 10)->nullable();
            $table->string('guest_uuid')->nullable();
            $table->boolean('notifications_enabled')->default(true);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'device_type']);
            $table->index('guest_uuid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_devices');
    }
};
