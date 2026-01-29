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
            if (!Schema::hasColumn('users', 'company_name')) {
                $table->string('company_name')->nullable()->after('account_type');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('users', 'office_request_status')) {
                $table->string('office_request_status')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'office_rejection_reason')) {
                $table->text('office_rejection_reason')->nullable()->after('office_request_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'address', 'office_request_status', 'office_rejection_reason']);
        });
    }
};
