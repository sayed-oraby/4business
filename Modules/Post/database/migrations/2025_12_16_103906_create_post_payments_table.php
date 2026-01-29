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
        Schema::create('post_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->string('ref_number')->unique();
            $table->string('provider')->default('sadad');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('KWD');
            $table->string('status')->default('pending'); // pending, paid, failed, cancelled
            $table->string('invoice_url')->nullable();
            $table->string('callback_url')->nullable();
            $table->json('payload')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            
            $table->index(['post_id', 'status']);
            $table->index('ref_number');
        });

        Schema::create('post_payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_payment_id')->constrained('post_payments')->cascadeOnDelete();
            $table->string('direction'); // request, response, webhook, error
            $table->integer('status_code')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
            
            $table->index('post_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_payment_logs');
        Schema::dropIfExists('post_payments');
    }
};

