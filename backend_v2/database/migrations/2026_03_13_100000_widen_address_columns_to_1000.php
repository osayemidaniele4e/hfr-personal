<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Widen physical_location and postal_address to VARCHAR(1000)
     * in hs_hospitals_history.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE hs_hospitals_history MODIFY physical_location VARCHAR(1000) NULL');
        DB::statement('ALTER TABLE hs_hospitals_history MODIFY postal_address VARCHAR(1000) NULL');
    }

    /**
     * Revert to the previous column sizes.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE hs_hospitals_history MODIFY physical_location VARCHAR(100) NULL');
        DB::statement('ALTER TABLE hs_hospitals_history MODIFY postal_address VARCHAR(500) NULL');
    }
};
