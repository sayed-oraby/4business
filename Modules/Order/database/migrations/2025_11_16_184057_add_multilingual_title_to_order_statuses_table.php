<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, convert existing string titles to JSON format
        DB::table('order_statuses')->get()->each(function ($status) {
            $titleValue = $status->title;

            // If already JSON, skip
            if (is_string($titleValue)) {
                $decoded = json_decode($titleValue, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return;
                }
            }

            // Convert string to JSON
            $titleJson = json_encode([
                'en' => $titleValue,
                'ar' => $titleValue,
            ]);

            DB::table('order_statuses')
                ->where('id', $status->id)
                ->update(['title' => $titleJson]);
        });

        // Now change the column type to JSON
        Schema::table('order_statuses', function (Blueprint $table) {
            $table->json('title')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Convert JSON back to string (use English as default)
        DB::table('order_statuses')->get()->each(function ($status) {
            $title = $status->title;
            if (is_string($title)) {
                $decoded = json_decode($title, true);
                if (is_array($decoded) && isset($decoded['en'])) {
                    $title = $decoded['en'];
                }
            }

            DB::table('order_statuses')
                ->where('id', $status->id)
                ->update(['title' => $title]);
        });

        Schema::table('order_statuses', function (Blueprint $table) {
            $table->string('title')->change();
        });
    }
};
