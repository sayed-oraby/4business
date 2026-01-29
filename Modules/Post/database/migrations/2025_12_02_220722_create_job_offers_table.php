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
        Schema::create('job_offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(\Modules\Post\Models\Post::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\Modules\User\Models\User::class)->constrained()->cascadeOnDelete();
            $table->date('joining_date');
            $table->decimal('salary', 10, 2);
            $table->text('description');
            $table->string('status')->default('pending'); // pending, accepted, rejected
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_offers');
    }
};
