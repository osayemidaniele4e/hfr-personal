<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospital_imports', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id', 36)->index();
            $table->unsignedBigInteger('uploaded_by');

            // Signature / identity
            $table->string('state_unique_id', 100)->nullable();
            $table->string('registration_no', 100)->nullable();
            $table->date('start_date')->nullable();
            $table->date('close_date')->nullable();

            // Facility data fields
            $table->string('facility_name', 200);
            $table->string('alt_facility_name', 200)->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('lga_id')->nullable();
            $table->unsignedBigInteger('ward_id')->nullable();
            $table->unsignedBigInteger('ownership_id')->nullable();
            $table->unsignedBigInteger('ownership_type_id')->nullable();
            $table->unsignedBigInteger('facility_level_id')->nullable();
            $table->unsignedBigInteger('facility_level_option_id')->nullable();
            $table->unsignedBigInteger('facility_level_options_category_id')->nullable();
            $table->string('physical_location', 500)->nullable();
            $table->string('postal_address', 500)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->string('phone_number', 50)->nullable();
            $table->string('alternate_number', 50)->nullable();
            $table->string('email_address', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('operational_days', 255)->nullable();
            $table->string('operational_hours', 100)->nullable();
            $table->unsignedBigInteger('operational_status_id')->nullable();
            $table->unsignedBigInteger('registration_status_id')->nullable();
            $table->unsignedBigInteger('license_status_id')->nullable();

            // Service domain
            $table->string('outpatient', 10)->nullable();
            $table->string('inpatient', 10)->nullable();

            // Human resources
            $table->unsignedInteger('doctors')->nullable();
            $table->unsignedInteger('pharmacists')->nullable();
            $table->unsignedInteger('dentist')->nullable();
            $table->unsignedInteger('pharmacy_technicians')->nullable();
            $table->unsignedInteger('nurses')->nullable();
            $table->unsignedInteger('lab_scientists')->nullable();
            $table->unsignedInteger('midwifes')->nullable();
            $table->unsignedInteger('lab_technicians')->nullable();
            $table->unsignedInteger('nurse_midwife')->nullable();
            $table->unsignedInteger('him_officers')->nullable();
            $table->unsignedInteger('community_health_officer')->nullable();
            $table->unsignedInteger('community_extension_workers')->nullable();
            $table->unsignedInteger('jun_community_extension_worker')->nullable();
            $table->unsignedInteger('dental_technicians')->nullable();
            $table->unsignedInteger('env_health_officers')->nullable();
            $table->unsignedInteger('attendants')->nullable();

            // Beds & facilities
            $table->unsignedInteger('beds')->nullable();
            $table->string('onsite_laboratory', 10)->nullable();
            $table->string('onsite_imaging', 10)->nullable();
            $table->string('onsite_pharmarcy', 10)->nullable();
            $table->string('mortuary_services', 10)->nullable();
            $table->string('ambulance_services', 10)->nullable();

            // Validation
            $table->text('validation_errors')->nullable();
            $table->boolean('is_valid')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospital_imports');
    }
};
