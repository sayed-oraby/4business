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
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
            if (!Schema::hasColumn('packages', 'is_free')) {
                $table->boolean('is_free')->default(false)->after('is_featured');
            }
            if (!Schema::hasColumn('packages', 'free_credits_per_user')) {
                $table->integer('free_credits_per_user')->nullable()->after('is_free');
            }
            if (!Schema::hasColumn('packages', 'top_days')) {
                $table->integer('top_days')->default(0)->after('period_days');
            }
            if (!Schema::hasColumn('packages', 'label_color')) {
                $table->string('label_color', 7)->default('#3b82f6')->after('top_days');
            }
            if (!Schema::hasColumn('packages', 'card_color')) {
                $table->string('card_color', 7)->default('#eff6ff')->after('label_color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $columns = ['is_featured', 'is_free', 'free_credits_per_user', 'top_days', 'label_color', 'card_color'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('packages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
