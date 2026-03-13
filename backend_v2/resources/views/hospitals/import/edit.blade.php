@extends('layouts.master')

@section('content-title')
    Edit Import Record
    <a href="{{ route('hospitals.import.preview', $batchId) }}">
        <button type="button" class="btn btn-default pull-right">
            <i class="fa fa-arrow-left"></i> Back to Preview
        </button>
    </a>
@endsection

@section('content')

    @if ($record->validation_errors)
        <div class="callout callout-danger">
            <h4><i class="fa fa-exclamation-triangle"></i> Validation Errors</h4>
            <ul>
                @foreach (json_decode($record->validation_errors, true) as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hospitals.import.update', [$batchId, $record->id]) }}">
        @csrf
        @method('PUT')

        {{-- Panel 1: Basic Information --}}
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Basic Information</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('facility_name') ? 'has-error' : '' }}">
                            <label>Facility Name <font color="red">*</font></label>
                            <input type="text" class="form-control" name="facility_name"
                                value="{{ old('facility_name', $record->facility_name) }}" required>
                            @error('facility_name') <span class="help-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Alternate Facility Name</label>
                            <input type="text" class="form-control" name="alt_facility_name"
                                value="{{ old('alt_facility_name', $record->alt_facility_name) }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>State Unique ID</label>
                            <input type="text" class="form-control" name="state_unique_id"
                                value="{{ old('state_unique_id', $record->state_unique_id) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Registration No</label>
                            <input type="text" class="form-control" name="registration_no"
                                value="{{ old('registration_no', $record->registration_no) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('start_date') ? 'has-error' : '' }}">
                            <label>Start Date <font color="red">*</font></label>
                            <input type="date" class="form-control" name="start_date"
                                value="{{ old('start_date', $record->start_date ? \Carbon\Carbon::parse($record->start_date)->format('Y-m-d') : '') }}"
                                required>
                            @error('start_date') <span class="help-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel 2: Location --}}
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Location</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('state_id') ? 'has-error' : '' }}">
                            <label>State <font color="red">*</font></label>
                            <select class="form-control select2" name="state_id" id="state_id" required style="width:100%;">
                                <option value="">--Select State--</option>
                                @foreach (getStates() as $st)
                                    <option value="{{ $st->id }}"
                                        {{ old('state_id', $record->state_id) == $st->id ? 'selected' : '' }}>
                                        {{ $st->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('state_id') <span class="help-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('lga_id') ? 'has-error' : '' }}">
                            <label>LGA <font color="red">*</font></label>
                            <select class="form-control select2" name="lga_id" id="lga_id" required style="width:100%;">
                                <option value="">--Select LGA--</option>
                            </select>
                            @error('lga_id') <span class="help-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('ward_id') ? 'has-error' : '' }}">
                            <label>Ward <font color="red">*</font></label>
                            <select class="form-control select2" name="ward_id" id="ward_id" required style="width:100%;">
                                <option value="">--Select Ward--</option>
                            </select>
                            @error('ward_id') <span class="help-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Physical Location</label>
                            <input type="text" class="form-control" name="physical_location"
                                value="{{ old('physical_location', $record->physical_location) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Postal Address</label>
                            <input type="text" class="form-control" name="postal_address"
                                value="{{ old('postal_address', $record->postal_address) }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('latitude') ? 'has-error' : '' }}">
                            <label>Latitude</label>
                            <input type="text" class="form-control" name="latitude"
                                value="{{ old('latitude', $record->latitude) }}">
                            @error('latitude') <span class="help-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('longitude') ? 'has-error' : '' }}">
                            <label>Longitude</label>
                            <input type="text" class="form-control" name="longitude"
                                value="{{ old('longitude', $record->longitude) }}">
                            @error('longitude') <span class="help-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" class="form-control" name="phone_number"
                                value="{{ old('phone_number', $record->phone_number) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Alternate Number</label>
                            <input type="text" class="form-control" name="alternate_number"
                                value="{{ old('alternate_number', $record->alternate_number) }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('email_address') ? 'has-error' : '' }}">
                            <label>Email Address</label>
                            <input type="email" class="form-control" name="email_address"
                                value="{{ old('email_address', $record->email_address) }}">
                            @error('email_address') <span class="help-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Website</label>
                            <input type="text" class="form-control" name="website"
                                value="{{ old('website', $record->website) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel 3: Classification --}}
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Classification & Status</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('ownership_id') ? 'has-error' : '' }}">
                            <label>Ownership <font color="red">*</font></label>
                            <select class="form-control select2" name="ownership_id" required style="width:100%;">
                                <option value="">--Select--</option>
                                @foreach (getOwnership() as $o)
                                    <option value="{{ $o->id }}"
                                        {{ old('ownership_id', $record->ownership_id) == $o->id ? 'selected' : '' }}>
                                        {{ $o->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ownership_id') <span class="help-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('ownership_type_id') ? 'has-error' : '' }}">
                            <label>Ownership Type <font color="red">*</font></label>
                            <input type="number" class="form-control" name="ownership_type_id"
                                value="{{ old('ownership_type_id', $record->ownership_type_id) }}" required>
                            @error('ownership_type_id') <span class="help-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('facility_level_id') ? 'has-error' : '' }}">
                            <label>Facility Level <font color="red">*</font></label>
                            <select class="form-control select2" name="facility_level_id" required style="width:100%;">
                                <option value="">--Select--</option>
                                @foreach (getLevelOfCare() as $l)
                                    <option value="{{ $l->id }}"
                                        {{ old('facility_level_id', $record->facility_level_id) == $l->id ? 'selected' : '' }}>
                                        {{ $l->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('facility_level_id') <span class="help-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Facility Level Option</label>
                            <input type="number" class="form-control" name="facility_level_option_id"
                                value="{{ old('facility_level_option_id', $record->facility_level_option_id) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('operational_status_id') ? 'has-error' : '' }}">
                            <label>Operational Status <font color="red">*</font></label>
                            <select class="form-control select2" name="operational_status_id" required style="width:100%;">
                                <option value="">--Select--</option>
                                @foreach (getOperationalStatus() as $os)
                                    <option value="{{ $os->id }}"
                                        {{ old('operational_status_id', $record->operational_status_id) == $os->id ? 'selected' : '' }}>
                                        {{ $os->status }}
                                    </option>
                                @endforeach
                            </select>
                            @error('operational_status_id') <span class="help-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Registration Status</label>
                            <select class="form-control select2" name="registration_status_id" style="width:100%;">
                                <option value="">--Select--</option>
                                @foreach (getRegistrationStatus() as $rs)
                                    <option value="{{ $rs->id }}"
                                        {{ old('registration_status_id', $record->registration_status_id) == $rs->id ? 'selected' : '' }}>
                                        {{ $rs->status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>License Status</label>
                            <select class="form-control select2" name="license_status_id" style="width:100%;">
                                <option value="">--Select--</option>
                                @foreach (getLicenseStatus() as $ls)
                                    <option value="{{ $ls->id }}"
                                        {{ old('license_status_id', $record->license_status_id) == $ls->id ? 'selected' : '' }}>
                                        {{ $ls->status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Operational Days</label>
                            <input type="text" class="form-control" name="operational_days"
                                placeholder="e.g. Monday,Tuesday,Wednesday"
                                value="{{ old('operational_days', $record->operational_days) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Operational Hours</label>
                            <input type="text" class="form-control" name="operational_hours"
                                placeholder="e.g. 8am - 5pm"
                                value="{{ old('operational_hours', $record->operational_hours) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel 4: Human Resources --}}
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Human Resources</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    @php
                        $hrFields = [
                            'doctors' => 'Doctors',
                            'dentist' => 'Dentists',
                            'pharmacists' => 'Pharmacists',
                            'pharmacy_technicians' => 'Pharmacy Technicians',
                            'nurses' => 'Nurses',
                            'midwifes' => 'Midwifes',
                            'nurse_midwife' => 'Nurse Midwife',
                            'lab_scientists' => 'Lab Scientists',
                            'lab_technicians' => 'Lab Technicians',
                            'him_officers' => 'HIM Officers',
                            'community_health_officer' => 'Community Health Officer',
                            'community_extension_workers' => 'Community Extension Workers',
                            'jun_community_extension_worker' => 'Junior Community Extension Worker',
                            'dental_technicians' => 'Dental Technicians',
                            'env_health_officers' => 'Env Health Officers',
                            'attendants' => 'Attendants',
                        ];
                    @endphp
                    @foreach ($hrFields as $field => $label)
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ $label }}</label>
                                <input type="number" class="form-control" name="{{ $field }}" min="0"
                                    value="{{ old($field, $record->$field) }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Panel 5: Services --}}
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Services</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    @php
                        $serviceFields = [
                            'outpatient' => 'Outpatient',
                            'inpatient' => 'Inpatient',
                            'onsite_laboratory' => 'Onsite Laboratory',
                            'onsite_imaging' => 'Onsite Imaging',
                            'onsite_pharmarcy' => 'Onsite Pharmacy',
                            'mortuary_services' => 'Mortuary Services',
                            'ambulance_services' => 'Ambulance Services',
                        ];
                    @endphp
                    @foreach ($serviceFields as $field => $label)
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ $label }}</label>
                                <select class="form-control" name="{{ $field }}">
                                    <option value="">--Select--</option>
                                    <option value="Yes" {{ old($field, $record->$field) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="No" {{ old($field, $record->$field) == 'No' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Beds</label>
                            <input type="number" class="form-control" name="beds" min="0"
                                value="{{ old('beds', $record->beds) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fa fa-save"></i> Update Record
                </button>
                <a href="{{ route('hospitals.import.preview', $batchId) }}" class="btn btn-default btn-lg">
                    Cancel
                </a>
            </div>
        </div>
        <br>
    </form>

@endsection

@push('bk_script')
    @include('partials.notification')
    @include('partials.dynamic_state_script')

    <script>
        $(document).ready(function() {
            $('.select2').select2();

            // Load LGAs for the current state
            var currentStateId = "{{ old('state_id', $record->state_id) }}";
            var currentLgaId = "{{ old('lga_id', $record->lga_id) }}";
            var currentWardId = "{{ old('ward_id', $record->ward_id) }}";

            if (currentStateId) {
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('getLgaList') }}",
                    method: "POST",
                    data: { id: currentStateId, _token: _token },
                    success: function(result) {
                        $('#lga_id').html(result);
                        if (currentLgaId) {
                            $('#lga_id').val(currentLgaId).trigger('change');
                        }
                    }
                });
            }

            // Load wards for the current LGA
            if (currentLgaId) {
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('getWardList') }}",
                    method: "POST",
                    data: { lgaId: currentLgaId, _token: _token },
                    success: function(result) {
                        $('#ward_id').html(result);
                        if (currentWardId) {
                            $('#ward_id').val(currentWardId).trigger('change');
                        }
                    }
                });
            }
        });
    </script>
@endpush
