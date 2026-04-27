<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Widen narrow string columns in hs_hospitals_history to prevent
     * "Data too long" errors during bulk import.
     *
     * Skips when the table is absent (fresh DBs): hs_hospitals_history is legacy schema
     * not created by Laravel migrations; import a dump or add a baseline migration if needed.
     */
    public function up(): void
    {
        if (! Schema::hasTable('hs_hospitals_history')) {
            return;
        }

        DB::statement('ALTER TABLE hs_hospitals_history MODIFY registration_no VARCHAR(100) NULL');
        DB::statement('ALTER TABLE hs_hospitals_history MODIFY state_unique_id VARCHAR(100) NULL');
        DB::statement('ALTER TABLE hs_hospitals_history MODIFY postal_address VARCHAR(500) NULL');
        DB::statement('ALTER TABLE hs_hospitals_history MODIFY operational_hours VARCHAR(100) NULL');
        DB::statement('ALTER TABLE hs_hospitals_history MODIFY email_address VARCHAR(255) NULL');
    }

    /**
     * Revert to the original column sizes.
     */
    public function down(): void
    {
        if (! Schema::hasTable('hs_hospitals_history')) {
            return;
        }

        DB::statement('ALTER TABLE hs_hospitals_history MODIFY registration_no VARCHAR(20) NULL');
        DB::statement('ALTER TABLE hs_hospitals_history MODIFY state_unique_id VARCHAR(50) NULL');
        DB::statement('ALTER TABLE hs_hospitals_history MODIFY postal_address VARCHAR(100) NULL');
        DB::statement('ALTER TABLE hs_hospitals_history MODIFY operational_hours VARCHAR(11) NULL');
        DB::statement('ALTER TABLE hs_hospitals_history MODIFY email_address VARCHAR(100) NULL');
    }
};
