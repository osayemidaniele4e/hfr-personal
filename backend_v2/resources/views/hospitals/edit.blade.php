@extends('layouts.master')

@section('bk_css')
    <link rel="stylesheet" href="{{ asset('dist/iCheck/minimal/green.css') }}" />
@endsection



@section('content')
    <form class="form-horizontal" action="{{ route('hospitals.update', $hosp->id) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <input type="hidden" name="verified_by" value="">
        <input type="hidden" name="verified_at" value="">
        <input type="hidden" name="validated_at" value="">
        <input type="hidden" name="validated_by" value="">
        <input type="hidden" name="published_by" value="">
        <input type="hidden" name="published_at" value="">

        <div>
            @if (!in_array($status, [0, 6, 13]))
                <div class="callout callout-danger">
                    <h4>Warning!</h4>
                    <p>There is another request for this facility on approval process.
                        Please wait for to the request to be published before you submit another request!</p>
                </div>
            @endif
        </div>

        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            {{-- Tab One   --}}
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                            aria-expanded="true" aria-controls="collapseOne">
                            Signature Domain
                        </a>
                    </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">
                        <div class="box-body">

                            <div class="form-group">
                                <label for="cac_reg" class="col-sm-2 control-label">State Unique ID:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="state_unique_id" name="state_unique_id"
                                        value="{{ $hosp->state_unique_id }}" placeholder="State Unique Identifier">
                                </div>
                                <label for="cac_reg" class="col-sm-2 control-label">Registration No:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="registration_no" name="registration_no"
                                        value="{{ $hosp->registration_no }}"
                                        placeholder="Corporate Affairs Registration Number">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('facility_name') ? 'has-error' : '' }}">
                                        <label for="reg_fac_name" class="col-sm-4 control-label">Registered Name: <font
                                                color="red">*</font> </label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="facility_name"
                                                name="facility_name" value="{{ $hosp->facility_name }}"
                                                placeholder="Registered Facility Name">
                                            @if ($errors->has('facility_name'))
                                                <span class="help-block">
                                                    {{ $errors->first('facility_name') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('alt_facility_name') ? 'has-error' : '' }}">
                                        <label for="alt_facility_name" class="col-sm-4 control-label">Alternate
                                            Name:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="alt_facility_name"
                                                name="alt_facility_name" value="{{ $hosp->alt_facility_name }}"
                                                placeholder="Alternate Facility Name">
                                            @if ($errors->has('alt_facility_name'))
                                                <span class="help-block">
                                                    {{ $errors->first('alt_facility_name') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Commencement Date:<font color="red">*</font>
                                </label> </label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="start_date"
                                            name="start_date" value="{{ $hosp->start_date }}" autocomplete="off"
                                            required>
                                    </div>
                                </div>
                                <label class="col-sm-2 control-label">State:<font color="red">*</font> </label></label>
                                <div class="col-sm-4">
                                    <select class="form-control select2 dynamic" id="state_id" name ="state_id"
                                        data-dependent="lga_id" disabled required>
                                        <option value="">--Select State--</option>

                                        @foreach (getStates() as $st)
                                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                                        @endforeach

                                    </select>
                                    <input type="hidden" name="state_id" value="{{ $hosp->state_id }}">


                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('lga_id') ? 'has-error' : '' }}">
                                        <label class="col-sm-4 control-label">LGA:<font color="red">*</font>
                                        </label></label>
                                        <div class="col-sm-8">
                                            <select class="form-control select2 dynamic" id="lga_id" name="lga_id"
                                                data-dependent="ward_id">

                                            </select>
                                            @if ($errors->has('lga_id'))
                                                <span class="help-block">
                                                    {{ $errors->first('lga_id') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('ward_id') ? 'has-error' : '' }}">
                                        <label class="col-sm-4 control-label">Ward:<font color="red">*</font> </label>
                                        <div class="col-sm-8">
                                            <select class="form-control select2" id="ward_id" name="ward_id"
                                                style="width: 100%;">

                                            </select>
                                            @if ($errors->has('ward_id'))
                                                <span class="help-block">
                                                    {{ $errors->first('ward_id') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('facility_level_id') ? 'has-error' : '' }}">
                                        <label class="col-sm-4 control-label">Hospital/ Clinic Level:<font color="red">
                                                *</font> </label></label>
                                        <div class="col-sm-8">
                                            <select class="form-control select2" id="facility_level_id"
                                                name="facility_level_id" style="width: 100%;">
                                                <option value="">--Select Level of Care--</option>
                                                @foreach (getLevelOfcare() as $st)
                                                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('facility_level_id'))
                                                <span class="help-block">
                                                    {{ $errors->first('facility_level_id') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                                <label id="level_option_label" class="col-sm-2 control-label"
                                    style="display:none">Facility Level Options:</label>
                                <div id="level_option_div" class="col-sm-4" style="display:none">
                                    <select class="form-control select2" id="facility_level_option_id"
                                        name="facility_level_option_id" style="width: 100%;">

                                    </select>
                                </div>

                            </div>


                            <div class="form-group" id="specialized_div" style="display:none">
                                <label class="col-sm-2 control-label">Specialized Options:</label>
                                <div class="col-sm-4">
                                    <select class="form-control select2" id="facility_level_options_category_id"
                                        name="facility_level_options_category_id" style="width: 100%;">

                                    </select>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('ownership_id') ? 'has-error' : '' }}">
                                        <label class="col-sm-4 control-label">Ownership:<font color="red">*</font>
                                        </label></label>
                                        <div class="col-sm-8">
                                            <select class="form-control select2" id="ownership_id" name="ownership_id"
                                                style="width: 100%;">
                                                <option value="">--Select Ownership--</option>
                                                @foreach (getOwnership() as $st)
                                                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                                                @endforeach

                                            </select>
                                            @if ($errors->has('ownership_id'))
                                                <span class="help-block">
                                                    {{ $errors->first('ownership_id') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('ownership_type_id') ? 'has-error' : '' }}">
                                        <label class="col-sm-4 control-label">Ownership Type:<font color="red">*</font>
                                        </label>
                                        <div class="col-sm-8">
                                            <select class="form-control select2" id="ownership_type_id"
                                                name="ownership_type_id" style="width: 100%;">

                                            </select>
                                            @if ($errors->has('ownership_type_id'))
                                                <span class="help-block">
                                                    {{ $errors->first('ownership_type_id') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="house_no" class="col-sm-2 control-label"> Physical Location:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="physical_location"
                                        name="physical_location" value="{{ $hosp->physical_location }}"
                                        placeholder="Not P.O. Box or PMB">
                                </div>

                                <label for="street_name" class="col-sm-2 control-label"> Postal Address:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="postal_address" name="postal_address"
                                        value="{{ $hosp->postal_address }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('latitude') ? 'has-error' : '' }}">
                                        <label for="latitude" class="col-sm-4 control-label">Latitude:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="latitude" name="latitude"
                                                value="{{ $hosp->latitude }}">
                                            @if ($errors->has('latitude'))
                                                <span class="help-block">
                                                    {{ $errors->first('latitude') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('longitude') ? 'has-error' : '' }}">
                                        <label for="longitude" class="col-sm-4 control-label">Longitude:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="longitude" name="longitude"
                                                value="{{ $hosp->longitude }}">
                                            @if ($errors->has('longitude'))
                                                <span class="help-block">
                                                    {{ $errors->first('longitude') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                            </div>


                            <div class="form-group">
                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('phone_number') ? 'has-error' : '' }}">
                                        <label for="phone_number" class="col-sm-4 control-label">Phone Number:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="phone_number"
                                                name="phone_number" value="{{ $hosp->phone_number }}"
                                                data-inputmask='"mask": "0999-999-9999"' data-mask>
                                            @if ($errors->has('phone_number'))
                                                <span class="help-block">
                                                    {{ $errors->first('phone_number') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('alternate_number') ? 'has-error' : '' }}">
                                        <label for="postal_address" class="col-sm-4 control-label">Alternate
                                            Number:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="alternate_number"
                                                name="alternate_number" value="{{ $hosp->alternate_number }}"
                                                data-inputmask='"mask": "0999-999-9999"' data-mask>
                                            @if ($errors->has('alternate_number'))
                                                <span class="help-block">
                                                    {{ $errors->first('alternate_number') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                            </div>


                            <div class="form-group">
                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('email_address') ? 'has-error' : '' }}">
                                        <label for="email_address" class="col-sm-4 control-label">E-mail Address:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="email_address"
                                                name="email_address" value="{{ $hosp->email_address }}">
                                            @if ($errors->has('email_address'))
                                                <span class="help-block">
                                                    {{ $errors->first('email_address') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('website') ? 'has-error' : '' }}">
                                        <label for="website" class="col-sm-4 control-label">Website:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="website" name="website"
                                                value="{{ $hosp->website }}">
                                            @if ($errors->has('website'))
                                                <span class="help-block">
                                                    {{ $errors->first('website') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Days of Operation:</label>
                                <div class="col-sm-2">
                                    <input type='checkbox' id='all_days' value=''>Select all
                                </div>
                                <div class="col-sm-2">
                                    <input type='checkbox' id='d1' name='operational_days[]' value='Monday'
                                        {{ strpos($hosp->operational_days, 'Monday') !== false ? 'checked' : '' }}> Monday
                                </div>
                                <div class="col-sm-2">
                                    <input type='checkbox' id='d2' name='operational_days[]' value='Tuesday'
                                        {{ strpos($hosp->operational_days, 'Tuesday') !== false ? 'checked' : '' }}>
                                    Tuesday
                                </div>
                                <div class="col-sm-2">
                                    <input type='checkbox' id='d3' name='operational_days[]'
                                        value='Wednesday'{{ strpos($hosp->operational_days, 'Wednesday') !== false ? 'checked' : '' }}>
                                    Wednesday
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-2">
                                    <input type='checkbox' id='d4' name='operational_days[]'
                                        value='Thursday'{{ strpos($hosp->operational_days, 'Thursday') !== false ? 'checked' : '' }}>Thursday
                                </div>
                                <div class="col-sm-2">
                                    <input type='checkbox' id='d5' name='operational_days[]' value='Friday'
                                        {{ strpos($hosp->operational_days, 'Friday') !== false ? 'checked' : '' }}> Friday
                                </div>
                                <div class="col-sm-2">
                                    <input type='checkbox' id='d6' name='operational_days[]'
                                        value='Saturday'{{ strpos($hosp->operational_days, 'Saturday') !== false ? 'checked' : '' }}>
                                    Saturday
                                </div>
                                <div class="col-sm-2">
                                    <input type='checkbox' id='d7' name='operational_days[]'
                                        value='Sunday'{{ strpos($hosp->operational_days, 'Sunday') !== false ? 'checked' : '' }}>
                                    Sunday
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('operational_hours') ? 'has-error' : '' }}">
                                        <label class="col-sm-4 control-label">Hours of Operation:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="operational_hours"
                                                value="{{ $hosp->operational_hours }}"
                                                placeholder="24hrs / 08:00AM-06:00PM">
                                            @if ($errors->has('operational_hours'))
                                                <span class="help-block">
                                                    {{ $errors->first('operational_hours') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div
                                        class="form-group {{ $errors->has('operational_status_id') ? 'has-error' : '' }}">
                                        <label class="col-sm-4 control-label">Operation Status:<font color="red">*
                                            </font> </label></label>
                                        <div class="col-sm-8">
                                            <select class="form-control select2" id="operational_status_id"
                                                name="operational_status_id" style="width: 100%;">
                                                <option value="">--Select Operation Status--</option>
                                                @foreach (getOperationalStatus() as $st)
                                                    <option value="{{ $st->id }}">{{ $st->status }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('operational_status_id'))
                                                <span class="help-block">
                                                    {{ $errors->first('operational_status_id') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                            </div>
                            <div class="form-group" id ='close_date_div' hidden>
                                <div class="col-sm-6"></div>
                                <label class="col-sm-2 control-label">Close Date:<font color="red">*</font></label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="close_date"
                                            name="close_date" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group" id='reg_license_status'>

                                <label class="col-sm-2 control-label">Registration Status:</label>
                                <div class="col-sm-4">
                                    <select class="form-control select2" id="registration_status_id"
                                        name="registration_status_id" style="width: 100%;">
                                        <option value="0">--Select Registration Status--</option>
                                        @foreach (getRegistrationStatus() as $st)
                                            <option value="{{ $st->id }}">{{ $st->status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label">License Status:</label>
                                <div class="col-sm-4">
                                    <select class="form-control select2" id="license_status_id" name="license_status_id"
                                        style="width: 100%;">
                                        <option value="0">--Select License Status--</option>
                                        @foreach (getLicenseStatus() as $st)
                                            <option value="{{ $st->id }}">{{ $st->status }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>


                        </div>
                    </div>
                </div>
            </div> {{--  tab one ends  --}}

            {{-- Tab twoServices --}}
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="heading2">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                            href="#collapse2" aria-expanded="false" aria-controls="collapse2">
                            Service Domain
                        </a>
                    </h4>
                </div>
                <div id="collapse2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading2">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"> Service Type:</label>

                            <div class="col-sm-9">
                                <div class="col-sm-12">
                                    <input type='checkbox'name='outpatient' value='Yes'
                                        {{ $hosp->outpatient == 'Yes' ? 'checked' : '' }}> Out Patient
                                </div>
                                <div class="col-sm-12">
                                    <input type='checkbox'name='inpatient' value='Yes'
                                        {{ $hosp->inpatient == 'Yes' ? 'checked' : '' }}> In Patient
                                </div>
                            </div>
                        </div>
                        <hr size="30">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Medical Services:</label>
                            <div class="col-sm-9">
                                @foreach ($lst_services as $s)
                                    @if ($s->service_category_id == 1)
                                        <div class="col-sm-4">
                                            <input type='checkbox' name='services[]' value={{ $s->id }}
                                                {{ in_array($s->id, $current_services, true) ? 'checked' : '' }}>
                                            {{ $s->name }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <hr size="30">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Surgical Services:</label>
                            <div class="col-sm-9">
                                @foreach ($lst_services as $s)
                                    @if ($s->service_category_id == 2)
                                        <div class="col-sm-4">
                                            <input type='checkbox' name='services[]' value={{ $s->id }}
                                                {{ in_array($s->id, $current_services, true) ? 'checked' : '' }}>
                                            {{ $s->name }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <hr size="30">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Obstetrics and Gynecology Services:</label>
                            <div class="col-sm-9">
                                @foreach ($lst_services as $s)
                                    @if ($s->service_category_id == 3)
                                        <div class="col-sm-4">
                                            <input type='checkbox' name='services[]' value={{ $s->id }}
                                                {{ in_array($s->id, $current_services, true) ? 'checked' : '' }}>
                                            {{ $s->name }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <hr size="30">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Pediatrics Services:</label>
                            <div class="col-sm-9">
                                @foreach ($lst_services as $s)
                                    @if ($s->service_category_id == 4)
                                        <div class="col-sm-4">
                                            <input type='checkbox' name='services[]' value={{ $s->id }}
                                                {{ in_array($s->id, $current_services, true) ? 'checked' : '' }}>
                                            {{ $s->name }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <hr size="30">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Dental Services:</label>
                            <div class="col-sm-9">
                                @foreach ($lst_services as $s)
                                    @if ($s->service_category_id == 5)
                                        <div class="col-sm-4">
                                            <input type='checkbox' name='services[]' value={{ $s->id }}
                                                {{ in_array($s->id, $current_services, true) ? 'checked' : '' }}>
                                            {{ $s->name }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <hr size="30">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Specific Clinical Services:</label>
                            <div class="col-sm-9">
                                @foreach ($lst_services as $s)
                                    @if ($s->service_category_id == 6)
                                        <div class="col-sm-4">
                                            <input type='checkbox' name='services[]' value={{ $s->id }}
                                                {{ in_array($s->id, $current_services, true) ? 'checked' : '' }}>
                                            {{ $s->name }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <hr size="30">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Other Services:</label>
                            <div class="col-sm-9">
                                <div class="col-sm-4">
                                    <input type='checkbox'name='onsite_pharmarcy' value='Yes'
                                        {{ $hosp->onsite_pharmarcy == 'Yes' ? 'checked' : '' }}> Onsite Pharmacy
                                </div>
                                <div class="col-sm-4">
                                    <input type='checkbox'name='onsite_laboratory' value='Yes'
                                        {{ $hosp->onsite_laboratory == 'Yes' ? 'checked' : '' }}> Onsite Laboratory
                                </div>
                                <div class="col-sm-4">
                                    <input type='checkbox'name='mortuary_services'
                                        value='Yes'{{ $hosp->mortuary_services == 'Yes' ? 'checked' : '' }}> Mortuary
                                    Services
                                </div>
                                <div class="col-sm-4">
                                    <input type='checkbox'name='onsite_imaging'
                                        value='Yes'{{ $hosp->onsite_imaging == 'Yes' ? 'checked' : '' }}> Onsite
                                    Imaging/ Radio-Diagnostics Center
                                </div>
                                <div class="col-sm-4">
                                    <input type='checkbox'name='ambulance_services'
                                        value='Yes'{{ $hosp->ambulance_services == 'Yes' ? 'checked' : '' }}> Ambulance
                                    Services
                                </div>
                            </div>
                        </div>
                        <hr size="30">

                        <div class="form-group {{ $errors->has('beds') ? 'has-error' : '' }}">
                            <label for="hs_no_doctors" class="col-sm-3 control-label">Total number of beds:</label>

                            <div class="col-sm-9">
                                <div class="col-sm-12">
                                    <input type="text" class="form-control input-sm" id="beds" name="beds"
                                        value="{{ $hosp->beds }}">
                                    @if ($errors->has('beds'))
                                        <span class="help-block">
                                            {{ $errors->first('beds') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div><!-- end here-->
            {{-- Tab Four- HR --}}
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="heading3">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                            href="#collapse3" aria-expanded="false" aria-controls="collapse3">
                            Human Resources
                        </a>
                    </h4>
                </div>


                <div id="collapse3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading3">
                    <div class="panel-body">

                        <div class="form-group">
                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('doctors') ? 'has-error' : '' }}">
                                    <label for="hs_no_doctors" class="col-sm-8 control-label">Number of Medical
                                        Doctors:</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm" id="doctors"
                                            name="doctors" value="{{ $hosp->doctors }}">
                                        @if ($errors->has('doctors'))
                                            <span class="help-block">
                                                {{ $errors->first('doctors') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('dentist') ? 'has-error' : '' }}">
                                    <label for="hs_no_dentist" class="col-sm-8 control-label">Number of Dentists:</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm" id="dentist"
                                            name="dentist" value="{{ $hosp->doctors }}">
                                        @if ($errors->has('dentist'))
                                            <span class="help-block">
                                                {{ $errors->first('dentist') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('dental_technicians') ? 'has-error' : '' }}">
                                    <label for="hs_no_dental_tech" class="col-sm-8 control-label">Number of Dental
                                        Technicians:</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm" id="dental_technicians"
                                            name="dental_technicians" value="{{ $hosp->dental_technicians }}">
                                        @if ($errors->has('dental_technicians'))
                                            <span class="help-block">
                                                {{ $errors->first('dental_technicians') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('pharmacists') ? 'has-error' : '' }}">
                                    <label for="hs_no_pharm" class="col-sm-8 control-label">Number of Pharmacists:</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm" id="pharmacists"
                                            name="pharmacists" value="{{ $hosp->pharmacists }}">
                                        @if ($errors->has('pharmacists'))
                                            <span class="help-block">
                                                {{ $errors->first('pharmacists') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('pharmacy_technicians') ? 'has-error' : '' }}">
                                    <label for="hs_no_pharm_tech" class="col-sm-8 control-label">Number of Pharmacy
                                        Technicians:</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm" id="pharmacy_technicians"
                                            name="pharmacy_technicians" value="{{ $hosp->pharmacy_technicians }}">
                                        @if ($errors->has('pharmacy_technicians'))
                                            <span class="help-block">
                                                {{ $errors->first('pharmacy_technicians') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('lab_scientists') ? 'has-error' : '' }}">
                                    <label for="hs_no_lab_sc" class="col-sm-8 control-label">Number of Laboratory
                                        Scientists:</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm" id="lab_scientists"
                                            name="lab_scientists" value="{{ $hosp->lab_scientists }}">
                                        @if ($errors->has('lab_scientists'))
                                            <span class="help-block">
                                                {{ $errors->first('lab_scientists') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('lab_technicians') ? 'has-error' : '' }}">
                                    <label for="hs_no_lab_tech" class="col-sm-8 control-label">Number of Laboratory
                                        Technicians:</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm" id="lab_technicians"
                                            name="lab_technicians" value="{{ $hosp->lab_technicians }}">
                                        @if ($errors->has('lab_technicians'))
                                            <span class="help-block">
                                                {{ $errors->first('lab_technicians') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('nurses') ? 'has-error' : '' }}">
                                    <label for="hs_no_single_qualified_nurses" class="col-sm-8 control-label">Number of
                                        Nurses (Single Qualified):</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm" id="nurses"
                                            name="nurses" value="{{ $hosp->nurses }}">
                                        @if ($errors->has('nurses'))
                                            <span class="help-block">
                                                {{ $errors->first('nurses') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('midwifes') ? 'has-error' : '' }}">
                                    <label for="hs_no_single_qualified_midwives" class="col-sm-8 control-label">Number of
                                        Midwifes (Single Qualified):</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm" id="midwifes"
                                            name="midwifes" value="{{ $hosp->midwifes }}">
                                        @if ($errors->has('midwifes'))
                                            <span class="help-block">
                                                {{ $errors->first('midwifes') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('nurse_midwife') ? 'has-error' : '' }}">
                                    <label for="hs_nurses_midwives" class="col-sm-8 control-label">Number of Nurse and
                                        Midwife (Double Qualified):</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm" id="nurse_midwife"
                                            name="nurse_midwife" value="{{ $hosp->nurse_midwife }}">
                                        @if ($errors->has('nurse_midwife'))
                                            <span class="help-block">
                                                {{ $errors->first('nurse_midwife') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <div class="col-sm-6">
                                <div
                                    class="form-group {{ $errors->has('community_health_officer') ? 'has-error' : '' }}">
                                    <label for="hs_no_comm_health_officer" class="col-sm-8 control-label">Number of
                                        Community Health Officer:</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm" id="community_health_officer"
                                            name="community_health_officer"
                                            value="{{ $hosp->community_health_officer }}">
                                        @if ($errors->has('community_health_officer'))
                                            <span class="help-block">
                                                {{ $errors->first('community_health_officer') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div
                                    class="form-group {{ $errors->has('community_extension_workers') ? 'has-error' : '' }}">
                                    <label for="hs_no_comm_health_officer" class="col-sm-8 control-label">Number of
                                        Community Health Extension Workers:</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm"
                                            id="community_extension_workers" name="community_extension_workers"
                                            value="{{ $hosp->community_extension_workers }}">
                                        @if ($errors->has('community_extension_workers'))
                                            <span class="help-block">
                                                {{ $errors->first('community_extension_workers') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <div class="col-sm-6">
                                <div
                                    class="form-group {{ $errors->has('jun_community_extension_worker') ? 'has-error' : '' }}">
                                    <label for="hs_no_jun_comm_health_ext_off" class="col-sm-8 control-label">Number of
                                        Junior Com Health Extension Worker:</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm"
                                            id="jun_community_extension_worker" name="jun_community_extension_worker"
                                            value="{{ $hosp->jun_community_extension_worker }}">
                                        @if ($errors->has('jun_community_extension_worker'))
                                            <span class="help-block">
                                                {{ $errors->first('jun_community_extension_worker') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('env_health_officers') ? 'has-error' : '' }}">
                                    <label for="hs_no_env_health_officer" class="col-sm-8 control-label">Number of
                                        Environmental Health Officers:</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm" id="env_health_officers"
                                            name="env_health_officers" value="{{ $hosp->env_health_officers }}">
                                        @if ($errors->has('env_health_officers'))
                                            <span class="help-block">
                                                {{ $errors->first('env_health_officers') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('him_officers') ? 'has-error' : '' }}">
                                    <label for="hs_no_health_rec" class="col-sm-8 control-label">Number of Health Records
                                        / HIM Officers:</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm" id="him_officers"
                                            name="him_officers" value="{{ $hosp->him_officers }}">
                                        @if ($errors->has('him_officers'))
                                            <span class="help-block">
                                                {{ $errors->first('him_officers') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('attendants') ? 'has-error' : '' }}">
                                    <label for="hs_no_env_health_officer" class="col-sm-8 control-label">Number of Health
                                        Attendant/Assistant:</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm" id="attendants"
                                            name="attendants" value="{{ $hosp->attendants }}">
                                        @if ($errors->has('attendants'))
                                            <span class="help-block">
                                                {{ $errors->first('attendants') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div><!-- end here-->


            {{-- Tab three- HR --}}
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="heading4">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                            href="#collapse4" aria-expanded="false" aria-controls="collapse4">
                            Building Images
                        </a>
                    </h4>
                </div>
                <div id="collapse4" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading4">
                    <div class="panel-body">

                        <div class="form-group row">
                            <label for="images" class="col-sm-4 col-form-label">Images:</label>
                            <div class="col-sm-8">
                                <input type="file" class="form-control input-sm" id="images" name="images[]"
                                    multiple>

                                {{-- @error('images')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror --}}
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <!-- end here-->
        </div>

        <!-- /.box-body -->
        <div class="box-footer">
            <a href="{{ route('hospitals.index') }}">
                <button type="button" class="btn btn-warning">Return Back</button>
            </a>
            @if (in_array($status, [0, 6, 13]))
                <button type="submit" class="btn btn-primary pull-right">Submit Update Request</button>
            @endif

        </div>
        <!-- /.box-footer -->
    </form>
@endsection

@push('bk_script')
    @include('partials.dynamic_state_script')
    @include('partials.notification')

    <script src="{{ asset('dist/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('dist/Inputmask5/jquery.inputmask.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('input').iCheck({
                checkboxClass: 'icheckbox_minimal-green',
                increaseArea: '20%' // optional
            });

            $('#all_days').on('ifChecked', function(event) {
                $('#d1, #d2, #d3, #d4, #d5,#d6, #d7').iCheck('check');
            });
            $('#all_days').on('ifUnchecked', function(event) {
                $('#d1, #d2, #d3, #d4, #d5,#d6, #d7').iCheck('uncheck');
            });

            $('[data-mask]').inputmask();

            $('#start_date').datepicker({
                endDate: new Date(),
                autoclose: true,
            }).on("changeDate", function(e) {
                $('#close_date').datepicker('setStartDate', e.date);
            });

            $('#close_date').datepicker({
                autoclose: true,
                // startDate: new Date({{ $hosp->start_date }})
            });


        });
    </script>
    <script>
        //*********binding drop down values ****************************
        $("#state_id").val({{ $hosp->state_id }}).change();
        $("#facility_level_id").val({{ $hosp->facility_level_id }}).change();
        $("#ownership_id").val({{ $hosp->ownership_id }}).change();
        $("#operational_status_id").val("{{ $hosp->operational_status_id }}").change();
        $("#registration_status_id").val("{{ $hosp->registration_status_id }}").change();
        $("#license_status_id").val("{{ $hosp->license_status_id }}").change();

        // var assignedLgaID= {{ Auth::user()->lga_id }};
        var lgaPermission = "{{ implode(', ', auth()->user()->getDirectPermissions()->pluck('id')->toArray()) }}";

        //get lgas
        var stateID = {{ $hosp->state_id }};
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "{{ route('getLgaList') }}",
            method: "POST",
            data: {
                id: stateID,
                _token: _token
            },
            success: function(result) {
                $('#lga_id').html(result);
                $("#lga_id").val({{ $hosp->lga_id }});

                //remove lgas not assigned
                if (lgaPermission !== '1000') {
                    // $('#lga_id option[value !=' + assignedLgaID + ']').remove();
                    $("#lga_id > option").each(function() {
                        if (lgaPermission.indexOf(this.value) < 0) {
                            this.remove();
                        }
                    });
                }




            }
        })

        //get wards
        var lgaID = {{ $hosp->lga_id }};
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "{{ route('getWardList') }}",
            method: "POST",
            data: {
                lgaId: lgaID,
                _token: _token
            },
            success: function(result) {
                $('#ward_id').html(result);
                $('#ward_id').val({{ $hosp->ward_id }});
            }
        })

        //ownership type
        var own_id = "{{ $hosp->ownership_id }}";
        if (own_id != "") {
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('getOwnershipType') }}",
                method: "POST",
                data: {
                    ownership_id: own_id,
                    _token: _token
                },
                success: function(result) {
                    $('#ownership_type_id').html(result);
                    $('#ownership_type_id').val({{ $hosp->ownership_type_id }});
                }
            })
        }

        
        //facility level option
        var option_id = "{{ $hosp->facility_level_option_id }}";
        var levelID = "{{ $hosp->facility_level_id }}";
        if (levelID != "2") {
            $('#level_option_div').show();
            $('#level_option_label').show();

            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('getFacilityLevelOption') }}",
                method: "POST",
                data: {
                    id: levelID,
                    _token: _token
                },
                success: function(result) {
                    $('#facility_level_option_id').html(result);
                    $('#facility_level_option_id').val(option_id);
                }
            })
        }

        //specialized option
        var loc_id = "{{ $hosp->facility_level_options_category_id }}";
        var levOptionID = "{{ $hosp->facility_level_option_id }}";

        if (levOptionID == "5") //if specialized
        {
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('getSpecializedOptions') }}",
                method: "POST",
                data: {
                    _token: _token
                },
                success: function(result) {
                    $('#facility_level_options_category_id').html(result);
                    $('#facility_level_options_category_id').val(loc_id);
                    $('#specialized_div').show();
                }
            })
        }
        //************ ends binding**************************************

        /* hospital level change */
        $("#facility_level_id").change(function() {
            if ($(this).val() == "2") //if secondary
            {
                $('#facility_level_option_id option').remove();
                $('#level_option_label').hide();
                $('#level_option_div').hide();
                $('#facility_level_option_category_id option').remove();
                $('#specialized_div').hide();
            } else { //primary or tertiary
                $('#facility_level_option_id option').remove();
                $('#level_option_div').show();
                $('#level_option_label').show();
                $('#specialized_div').hide();
                $('#facility_level_option_category_id option').remove();


                var levelID = $('#facility_level_id').val();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('getFacilityLevelOption') }}",
                    method: "POST",
                    data: {
                        id: levelID,
                        _token: _token
                    },
                    success: function(result) {
                        $('#facility_level_option_id').html(result);
                    }
                })
            }
        });

        /* hospital level option change */
        $("#facility_level_option_id").change(function() {
            if ($(this).val() == "5") //if specialized
            {
                $('#specialized_div').show();

                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('getSpecializedOptions') }}",
                    method: "POST",
                    data: {
                        _token: _token
                    },
                    success: function(result) {
                        $('#facility_level_options_category_id').html(result);
                    }
                })
            } else {
                $('#facility_level_option_category_id option').remove();
                $('#specialized_div').hide();
            }
        });

        //get ownership  type ownership_type_id
        $("#ownership_id").change(function() {
            if ($(this).val() != "") //if specialized
            {
                var id = $('#ownership_id').val();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('getOwnershipType') }}",
                    method: "POST",
                    data: {
                        ownership_id: id,
                        _token: _token
                    },
                    success: function(result) {
                        $('#ownership_type_id').html(result);
                    }
                })

                //check if public is selected and hide registration and license status
                if ($(this).val() == 1) {
                    $('#registration_status_id').val(6).change();
                    $('#license_status_id').val(4).change();
                    // $('#reg_license_status').hide();
                } else {
                    $('#registration_status_id').val(0).change();
                    $('#license_status_id').val(0).change();
                    // $('#reg_license_status').show();
                }
            }
        });



        //if operation status is closed or temp close show close date
        $("#operational_status_id").change(function() {
            if ($(this).val() == 5 || $(this).val() == 6) {
                $('#close_date_div').show();
                $('#close_date').prop('required', true);
            } else {
                $('#close_date').prop('required', false);
                $('#close_date_div').hide();

            }
        });

        var op_id = "{{ $hosp->operational_status_id }}";
        if (op_id == 5 || op_id == 6) {
            $('#close_date_div').show();
            $('#close_date').prop('required', true);
        } else {
            $('#close_date').prop('required', false);
            $('#close_date_div').hide();
        }
    </script>
@endpush
