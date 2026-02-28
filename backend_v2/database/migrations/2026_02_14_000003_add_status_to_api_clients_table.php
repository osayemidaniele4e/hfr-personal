<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('api_clients', function (Blueprint $table) {
            // Status: pending (awaiting approval), approved, rejected
            $table->string('status', 20)->default('approved')->after('is_active');
            // Use case / reason for requesting access
            $table->text('use_case')->nullable()->after('description');
            // Rejection reason (filled by admin)
            $table->text('rejection_reason')->nullable()->after('use_case');
            // Who reviewed the request
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('rejection_reason');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');

            $table->index('status');
        });
    }

    public function down()
    {
        Schema::table('api_clients', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'use_case', 'rejection_reason', 'reviewed_by', 'reviewed_at']);
        });
    }
};
