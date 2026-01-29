<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('post_attachments');
        Schema::dropIfExists('post_skills');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('post_types');
        Schema::enableForeignKeyConstraints();

        Schema::create('post_types', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->decimal('price', 10, 2);
            $table->integer('period_days');
            $table->json('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_type_id')->constrained();
            $table->foreignId('package_id')->nullable()->constrained();
            $table->foreignId('city_id')->nullable()->constrained('shipping_cities');

            $table->json('title');
            $table->json('description');

            $table->integer('years_of_experience')->nullable();
            $table->string('nationality')->nullable();
            $table->string('gender')->nullable(); // male, female, both
            $table->string('full_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->date('birthdate')->nullable();
            $table->boolean('display_personal_details')->default(true);
            $table->string('cover_image')->nullable();

            $table->string('status')->default('pending'); // pending, approved, rejected, draft, expired
            $table->text('rejection_reason')->nullable();

            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_paid')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('post_skills', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->primary(['post_id', 'skill_id']);
        });

        Schema::create('post_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_type')->nullable(); // pdf, png, doc
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_attachments');
        Schema::dropIfExists('post_skills');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('post_types');
    }
};
