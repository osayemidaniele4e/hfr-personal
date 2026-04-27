<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create fhir_code_mappings table.
     *
     * Maps HFR lookup table values to FHIR R4 CodeSystem codes.
     * Follows the same pattern as dhis_lookup for DHIS2 integration.
     */
    public function up()
    {
        if (Schema::hasTable('fhir_code_mappings')) {
            return;
        }

        Schema::create('fhir_code_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('hfr_table', 100)->comment('Source lookup table name (e.g. lst_facility_types)');
            $table->string('hfr_id', 100)->comment('Primary key in the source lookup table (numeric or string code)');
            $table->string('hfr_display', 255)->comment('Display name from HFR lookup');
            $table->string('fhir_system', 500)->comment('FHIR CodeSystem URI');
            $table->string('fhir_code', 100)->comment('FHIR code value');
            $table->string('fhir_display', 255)->comment('FHIR display text');
            $table->string('resource_type', 50)->nullable()->comment('Target FHIR resource type (Organization, Location, etc.)');
            $table->string('element_path', 100)->nullable()->comment('Target FHIR element path (e.g. type, status)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['hfr_table', 'hfr_id', 'fhir_system'], 'fhir_code_mappings_unique');
            $table->index(['hfr_table', 'hfr_id']);
            $table->index('fhir_system');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fhir_code_mappings');
    }
};
