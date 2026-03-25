@extends('layouts.master')


@section('content-title')
    Hospitals and Clinics
    @if (auth()->user()->hasPermissionTo(2))
        <a href="{{ route('hospitals.create') }}">
            <button type="button" class="btn btn-primary pull-right">
                Add Hospital or Clinic
            </button>
        </a>
    @endif
@endsection

@section('content')


    <div class="box">
        <div class="box-header with-border">
            <form class="form-horizontal" action="{{ route('searchHospitalsAdmin') }}" method="get">
                @csrf

                <div class="form-group">
                    <div class="col-sm-3">
                        @if (Auth::user()->state_id == 1)
                            <select class="form-control select2" id="state_id" name ="state_id" style="width: 100%;">
                                <option value="1">--Select State--</option>
                                @foreach (getStates() as $st)
                                    <option value="{{ $st->id }}" {{ old('state_id') == $st->id ? 'selected' : '' }}>
                                        {{ $st->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <select class="form-control select2 dynamic" id="state_id" name ="state_id" disabled required
                                style="width: 100%;">
                                @foreach (getStates() as $st)
                                    <option value="{{ $st->id }}"
                                        {{ Auth::user()->state_id == $st->id ? 'selected' : '' }}>{{ $st->name }}
                                    </option>
                                @endforeach

                            </select>
                            <input type="hidden" name="state_id" value="{{ Auth::user()->state_id }}" />
                        @endif
                    </div>

                    <div class="col-sm-3">
                        <select class="form-control select2" id="lga_id" name="lga_id" style="width: 100%;">
                            <option value="1">--Select LGA--</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control select2" id="ward_id" name="ward_id" style="width: 100%;">
                            <option value="0">--Select Ward--</option>
                        </select>
                    </div>

                    <div class="col-sm-3">
                        <select class="form-control select2" id="facility_level_id" name="facility_level_id"
                            style="width: 100%;">
                            <option value="0">--Select Facility Level--</option>
                            @foreach (getLevelOfCare() as $st)
                                <option value="{{ $st->id }}"
                                    {{ old('facility_level_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                </div>
                <div class="form-group">
                    <div class="col-sm-3">
                        <select class="form-control select2" id="ownership_id" name="ownership_id" style="width: 100%;">
                            <option value="0">--Select Ownership--</option>
                            @foreach (getOwnership() as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control select2" id="operational_status_id" name ="operational_status_id"
                            style="width: 100%;">
                            <option value="0">--Select Operational Status--</option>
                            @foreach (getOperationalStatus() as $st)
                                <option value="{{ $st->id }}"
                                    {{ old('operational_status_id') == $st->id ? 'selected' : '' }}>{{ $st->status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control select2" id="registration_status_id" name="registration_status_id"
                            style="width: 100%;">
                            <option value="0">--Select Registration Status--</option>
                            @foreach (getRegistrationStatus() as $st)
                                <option value="{{ $st->id }}"
                                    {{ old('registration_status_id') == $st->id ? 'selected' : '' }}>{{ $st->status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control select2" id="license_status_id" name="license_status_id"
                            style="width: 100%;">
                            <option value="0">--Select License Status--</option>
                            @foreach (getLicenseStatus() as $st)
                                <option value="{{ $st->id }}"
                                    {{ old('license_status_id') == $st->id ? 'selected' : '' }}>{{ $st->status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="form-group">
                    <div class="col-sm-3">
                        <select class="form-control select2" id="geo_codes" name="geo_codes" style="width: 100%;">
                            <option value="0" {{ old('geo_codes') == 0 ? 'selected' : '' }}>--Select Coordinates--
                            </option>
                            <option value="1" {{ old('geo_codes') == 1 ? 'selected' : '' }}>With Coordinates</option>
                            <option value="2" {{ old('geo_codes') == 2 ? 'selected' : '' }}>With No Coordinates
                            </option>
                        </select>
                    </div>


                    <div class="col-sm-6">
                        <input class="form-control input-sm"type="text" name="facility_name" id="facility_name"
                            value="{{ old('facility_name') }}" class="form-control" placeholder="Facility name">
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-sm pull-right btn-block" id='reset'>Reset</button>
                            </div>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success btn-block btn-sm">Search</button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>


        <div class="box-body">

            <table id="table1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>State</th>
                        <th>LGA</th>
                        <th>Ward</th>
                        <th>UID</th>
                        <th>Facility Name</th>
                        <th>Facility Level</th>
                        <th>Ownership</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($facilities as $fac)
                        <tr>
                            <td>{{ $fac->state_id }}</td>
                            <td>{{ $fac->lga_id }}</td>
                            <td>{{ $fac->ward_id }}</td>
                            <td>{{ $fac->id }}</td>
                            <td>{{ $fac->facility_name }}</td>
                            <td>{{ $fac->facility_level_id }}</td>
                            <td>{{ $fac->ownership_id }}</td>
                            <td>

                                <a href="#">
                                    <button class="btn btn-success btn-sm" type="button" data-toggle="modal"
                                        data-target="#view_details" data-id="{{ $fac->id }}"
                                        data-unique_id="{{ $fac->unique_id }}"
                                        data-registration_no="{{ $fac->registration_no }}"
                                        data-start_date="{{ $fac->start_date }}"
                                        data-facility_name="{{ $fac->facility_name }}"
                                        data-alt_facility_name="{{ $fac->alt_facility_name }}"
                                        data-state="{{ $fac->state_id }}" data-lga="{{ $fac->lga_id }}"
                                        data-ward="{{ $fac->ward_id }}" data-ownership="{{ $fac->ownership_id }}"
                                        data-ownership_type="{{ $fac->ownership_type_id ? $fac->ownership_type_id : "" }}"
                                        data-facility_level="{{ $fac->facility_level_id }}"
                                        data-facility_level_option="{{ $fac->facility_level_option_id }}"
                                        data-physical_location="{{ $fac->physical_location }}"
                                        data-alternate_number="{{ $fac->alternate_number }}"
                                        data-longitude="{{ $fac->longitude }}" data-latitude="{{ $fac->latitude }}"
                                        data-postal_address="{{ $fac->postal_address }}"
                                        data-phone_number="{{ $fac->phone_number }}"
                                        data-email_address="{{ $fac->email_address }}"
                                        data-website="{{ $fac->website }}"
                                        data-operational_days="{{ $fac->operational_days }}"
                                        data-operational_hours="{{ $fac->operational_hours }}"
                                        {{-- data-operation_status="{{ $fac->operation_status }}" --}}
                                        data-registration_status="{{ $fac->registration_status_id }}"
                                        data-license_status="{{ $fac->license_status_id }}"
                                        data-doctors="{{ $fac->doctors }}" data-pharmacists="{{ $fac->pharmacists }}"
                                        data-dentist="{{ $fac->dentist }}"
                                        data-pharmacy_technicians="{{ $fac->pharmacy_technicians }}"
                                        data-nurses="{{ $fac->nurses }}"
                                        data-lab_scientists="{{ $fac->lab_scientists }}"
                                        data-midwifes="{{ $fac->midwifes }}"
                                        data-lab_technicians="{{ $fac->lab_technicians }}"
                                        data-nurse_midwife="{{ $fac->nurse_midwife }}"
                                        data-him_officers="{{ $fac->him_officers }}"
                                        data-community_health_officer="{{ $fac->community_health_officer }}"
                                        data-community_extension_workers="{{ $fac->community_extension_workers }}"
                                        data-jun_community_extension_worker="{{ $fac->jun_community_extension_worker }}"
                                        data-dental_technicians="{{ $fac->dental_technicians }}"
                                        data-env_health_officers="{{ $fac->env_health_officers }}"
                                        data-inpatient="{{ $fac->inpatient }}" data-outpatient="{{ $fac->outpatient }}"
                                        data-beds="{{ $fac->beds }}"
                                        data-onsite_laboratory="{{ $fac->onsite_laboratory }}"
                                        data-onsite_imaging="{{ $fac->onsite_imaging }}"
                                        data-onsite_pharmarcy="{{ $fac->onsite_pharmarcy }}"
                                        data-mortuary_services="{{ $fac->mortuary_services }}"
                                        data-attendants = "{{ $fac->attendants }}"
                                        data-ambulance_services="{{ $fac->ambulance_services }}"
                                        data-state_unique_id="{{ $fac->state_unique_id }}"
                                        data-outpatient = "{{ $fac->outpatient }}"
                                        data-inpatient="{{ $fac->inpatient }}">
                                        View
                                    </button>
                                </a>
                                @if (auth()->user()->hasAnyPermission([$fac->lga_id, 1000]))
                                    {{-- check if user has permission on the LGA --}}
                                    @if (auth()->user()->hasPermissionTo(3))
                                        <a href="{{ route('hospitals.edit', $fac->id) }}">
                                            <button class="btn btn-warning btn-sm" type="button">Edit</button>
                                        </a>
                                    @endif

                                    @if (auth()->user()->hasPermissionTo(4))
                                        {{-- hide delete button if the facility is on approval process --}}
                                        @if (in_array($fac->status_id, [0, 6, 13]))
                                            <a href="#">
                                                <button class="btn btn-danger btn-sm" type="button" data-toggle="modal"
                                                    data-target="#delete" data-id_del="{{ $fac->id }}"
                                                    data-unique_id_del="{{ $fac->unique_id }}"
                                                    data-facility_name_del="{{ $fac->facility_name }}"
                                                    data-state_id_del="{{ $fac->state_id }}">
                                                    Delete
                                                </button>
                                            </a>
                                        @endif
                                    @endif
                                @endif
                                @if (auth()->user()->hasPermissionTo(67))
                                    <a href="#">
                                        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal"
                                            data-target="#admin_update" data-id="{{ $fac->id }}"
                                            data-alt_name="{{ $fac->alt_facility_name }}"
                                            data-facility_name="{{ $fac->facility_name }}"
                                            data-long="{{ $fac->longitude }}" data-lati="{{ $fac->latitude }}"
                                            data-physical_location="{{ $fac->physical_location }}"
                                            data-postal_address="{{ $fac->postal_address }}">
                                            Update
                                        </button>
                                    </a>
                                @endif

                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>


        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <div class="row">

                @php
                    $perpage = $facilities->perpage();
                    $currentpage = $facilities->currentpage();
                    $from = ($currentpage - 1) * $perpage + 1;

                    if ($facilities->currentpage() == $facilities->lastpage()) {
                        $to = $facilities->total();
                    } else {
                        $to = $currentpage * $perpage;
                    }
                @endphp

                <div class="col-md-4">
                    Showing {{ $from }} to {{ $to }} of {{ $facilities->total() }} entries

                </div>
                <div class="col-md-8">
                    <div class="pull-right">
                        {{ $facilities->links() }}
                    </div>
                </div>

            </div>
            {{-- download buttons  --}}
            <div class="btn-group pull-right">
                <form class="form-horizontal" action="{{ route('hospitals.export') }}" method="post">
                    @csrf

                    <input type="hidden" name="state_id" value="{{ old('state_id') }}" />
                    <input type="hidden" name="lga_id" value="{{ old('lga_id') }}" />
                    <input type="hidden" name="ward_id" value="{{ old('ward_id') }}" />
                    <input type="hidden" name="facility_name" value="{{ old('facility_name') }}" />
                    <input type="hidden" name="geo_codes" value="{{ old('geo_codes') }}" />
                    <input type="hidden" name="facility_level_id" value="{{ old('facility_level_id') }}" />
                    <input type="hidden" name="ownership_id" value="{{ old('ownership_id') }}" />
                    <input type="hidden" name="operational_status_id" value="{{ old('operational_status_id') }}" />
                    <input type="hidden" name="registration_status_id" value="{{ old('registration_status_id') }}" />
                    <input type="hidden" name="license_status_id" value="{{ old('license_status_id') }}" />

                    <button type="submit" class="btn btn-primary btn-sm" name='format'>Download</button>
                </form>
            </div>


        </div>

    </div>
    <!-- /.box -->

    @include('hospitals.details_modal')

    {{-- modal deletation  --}}
    <div class="modal fade" id="delete" tabindex="-1" role="dialog">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Delete Facility</h4>
                    <div class='notifications top-right'></div>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('hospitals.InitiateDelete') }}">
                        @csrf
                        <input type="hidden" id="facility_id" name="facility_id">
                        <input type="hidden" id="facility_name_to_del" name="facility_name_to_del">
                        <input type="hidden" id="state_id_del" name="state_id_del">
                        <input type="hidden" name="null">


                        <div class="panel-body">

                            <div class="panel-group" id="accordion_d">
                                {{-- panel one --}}
                                <div class="panel panel-default">

                                    <div id="collapse1d" class="panel-collapse collapse in">
                                        <div class="panel-body">
                                            <div class="row">
                                                <label class="col-md-4">Unique_id:</label>
                                                <div class="col-md-8" id="unique_id_del"></div>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-md-right">Facility Name:</label>
                                                <div class="col-md-8" id="facility_name_del"> </div>
                                            </div>

                                            <div class="row">
                                                <label class="col-md-4">Reason for Delete:<font color="red">*</font>
                                                </label>
                                                <div class="col-md-8">
                                                    <textarea class="form-control" rows="3" name="reason" placeholder="Please enter reason" required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Submit Delete Request</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </form>

                </div><!--modal body ends -->
            </div><!--/.modal-content -->
        </div>
    </div> <!--/.modal -->

    {{-- modal admin update  --}}
    <div class="modal fade" id="admin_update" tabindex="-1" role="dialog">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Facility Information Correction</h4>
                    <div class='notifications top-right'></div>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('hospitals.adminupdate') }}">
                        @csrf
                        <input type="hidden" id="facility_id_x" name="facility_id_x">

                        <div class="panel-body">
                            <div class="form-group row">
                                <label for="reg_fac_name" class="col-sm-4 control-label">Registered Name: <font
                                        color="red">*</font> </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="facility_name_x"
                                        name="facility_name_x" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="reg_fac_name" class="col-sm-4 control-label">Alternate Name:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="alt_facility_name_x"
                                        name="alt_facility_name_x">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="house_no" class="col-sm-4 control-label"> Physical Location:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="physical_location_x"
                                        name="physical_location_x">
                                </div>

                            </div>

                            <div class="form-group row">
                                <label for="street_name" class="col-sm-4 control-label"> Postal Address:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="postal_address_x"
                                        name="postal_address_x">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="latitude" class="col-sm-4 control-label">Latitude:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="latitude_x" name="latitude_x">

                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="longitude" class="col-sm-4 control-label">Longitude:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="longitude_x" name="longitude_x">

                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Update</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </form>

                </div><!--modal body ends -->
            </div><!--/.modal-content -->
        </div>
    </div> <!--/.modal -->

@endsection


@push('bk_script')
    @include('partials.dynamic_state_script')
    @include('partials.notification')

    <script>
        $(document).ready(function() {


            //get lgas
            if ({{ Auth::user()->state_id }} > 1) {
                var stateID = {{ Auth::user()->state_id }};
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
                        $('#lga_id').val("{{ old('lga_id') }}");
                    }
                });
            }

            //get lgas after search
            if ("{{ old('state_id') }}" != "") {
                var stateID = "{{ old('state_id') }}";
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
                        $('#lga_id').val("{{ old('lga_id') }}");
                    }
                });
            }

            //get wards
            if ("{{ old('lga_id') }}" != 1) {
                var lgaID = "{{ old('lga_id') }}";
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
                        $('#ward_id').val({{ old('ward_id') }});
                    }
                });
            }



        });

        $("#reset").click(function() {
            $("#state_id").val(1).change();
            $("#lga_id").val(1).change();
            $("#ward_id").val(0).change();
            $("#geo_codes").val(0).change();
            $("#facility_name").val("");
            $("#facility_level_id").val(0).change();
            $("#ownership_id").val(0).change();
            $("#operational_status_id").val(0).change();
            $("#registration_status_id").val(0).change();
            $("#license_status_id").val(0).change();
        });



        $('#view_details').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var modal = $(this)
            $("#specialservice").empty();
            $("#medical").empty();
            $("#surgical").empty();
            $("#gyn").empty();
            $("#pediatrics").empty();
            $("#dental").empty();

            var days = button.data('operational_days');
            var operational_days = days.replace(/\,/g, ", ");

            modal.find('.modal-body #unique_id').text(button.data('unique_id'));
            modal.find('.modal-body #state_unique_id').text(button.data('state_unique_id'));
            modal.find('.modal-body #registration_no').text(button.data('registration_no'));
            modal.find('.modal-body #start_date').text(button.data('start_date'));
            modal.find('.modal-body #facility_name').text(button.data('facility_name'));
            modal.find('.modal-body #alt_facility_name').text(button.data('alt_facility_name'));
            modal.find('.modal-body #state').text(button.data('state'));
            modal.find('.modal-body #lga').text(button.data('lga'));
            modal.find('.modal-body #ward').text(button.data('ward'));
            modal.find('.modal-body #ownership').text(button.data('ownership'));
            modal.find('.modal-body #ownership_type').text(button.data('ownership_type'));
            modal.find('.modal-body #facility_level').text(button.data('facility_level'));
            modal.find('.modal-body #facility_level_option').text(button.data('facility_level_option'));
            modal.find('.modal-body #physical_location').text(button.data('physical_location'));
            modal.find('.modal-body #longitude').text(button.data('longitude'));
            modal.find('.modal-body #latitude').text(button.data('latitude'));
            modal.find('.modal-body #postal_address').text(button.data('postal_address'));
            modal.find('.modal-body #phone_number').text(button.data('phone_number'));
            modal.find('.modal-body #alternate_number').text(button.data('alternate_number'));
            modal.find('.modal-body #email_address').text(button.data('email_address'));
            modal.find('.modal-body #website').text(button.data('website'));
            modal.find('.modal-body #operational_days').text(operational_days);
            modal.find('.modal-body #operational_hours').text(button.data('operational_hours'));
            modal.find('.modal-body #operation_status').text(button.data('operation_status'));
            modal.find('.modal-body #registration_status').text(button.data('registration_status'));
            modal.find('.modal-body #license_status').text(button.data('license_status'));
            modal.find('.modal-body #doctors').text(button.data('doctors'));
            modal.find('.modal-body #pharmacists').text(button.data('pharmacists'));
            modal.find('.modal-body #dentist').text(button.data('dentist'));
            modal.find('.modal-body #pharmacy_technicians').text(button.data('pharmacy_technicians'));
            modal.find('.modal-body #nurses').text(button.data('nurses'));
            modal.find('.modal-body #lab_scientists').text(button.data('lab_scientists'));
            modal.find('.modal-body #attendants').text(button.data('attendants'));
            modal.find('.modal-body #midwifes').text(button.data('midwifes'));
            modal.find('.modal-body #lab_technicians').text(button.data('lab_technicians'));
            modal.find('.modal-body #nurse_midwife').text(button.data('nurse_midwife'));
            modal.find('.modal-body #him_officers').text(button.data('him_officers'));
            modal.find('.modal-body #community_health_officer').text(button.data('community_health_officer'));
            modal.find('.modal-body #community_extension_workers').text(button.data('community_extension_workers'));
            modal.find('.modal-body #jun_community_extension_worker').text(button.data(
                'jun_community_extension_worker'));
            modal.find('.modal-body #dental_technicians').text(button.data('dental_technicians'));
            modal.find('.modal-body #env_health_officers').text(button.data('env_health_officers'));
            modal.find('.modal-body #attendats').text(button.data('attendants'));
            modal.find('.modal-body #outpatient').text(button.data('outpatient'));
            modal.find('.modal-body #inpatient').text(button.data('inpatient'));
            modal.find('.modal-body #beds').text(button.data('beds'));
            modal.find('.modal-body #onsite_laboratory').text(button.data('onsite_laboratory'));
            modal.find('.modal-body #onsite_imaging').text(button.data('onsite_imaging'));
            modal.find('.modal-body #onsite_pharmarcy').text(button.data('onsite_pharmarcy'));
            modal.find('.modal-body #mortuary_services').text(button.data('mortuary_services'));
            modal.find('.modal-body #ambulance_services').text(button.data('ambulance_services'));

            var hosp_id = button.data('id');
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('hospitals.getServices') }}",
                method: "POST",
                data: {
                    hosp_id: hosp_id,
                    _token: _token
                },
                success: function(result) {
                    $.each(result, function(i, item) {
                        if (item.category_id == "1") {
                            $("#medical").append("<span class='label label-default'>" + item
                                .name + "</span> ");
                        }
                        if (item.category_id == "2") {
                            $("#surgical").append("<span class='label label-default'>" + item
                                .name + "</span> ");
                        }
                        if (item.category_id == "3") {
                            $("#gyn").append("<span class='label label-default'>" + item.name +
                                "</span> ");
                        }
                        if (item.category_id == "4") {
                            $("#pediatrics").append("<span class='label label-default'>" + item
                                .name + "</span> ");
                        }
                        if (item.category_id == "5") {
                            $("#dental").append("<span class='label label-default'>" + item
                                .name + "</span> ");
                        }
                        if (item.category_id == "6") {
                            $("#specialservice").append("<span class='label label-default'>" +
                                item.name + "</span> ");
                        }
                    });

                }
            })
        }); //end

        $('#delete').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var modal = $(this)

            modal.find('.modal-body #unique_id_del').text(button.data('unique_id_del'));
            modal.find('.modal-body #facility_name_del').text(button.data('facility_name_del'));
            modal.find('.modal-body #facility_id').val(button.data('id_del'));
            modal.find('.modal-body #facility_name_to_del').val(button.data('facility_name_del'));
            modal.find('.modal-body #state_id_del').val(button.data('state_id_del'));

        }); //end

        //admin update
        $('#admin_update').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var modal = $(this)

            modal.find('.modal-body #facility_name_x').val(button.data('facility_name'));
            modal.find('.modal-body #alt_facility_name_x').val(button.data('alt_name'));
            modal.find('.modal-body #facility_id_x').val(button.data('id'));
            modal.find('.modal-body #latitude_x').val(button.data('lati'));
            modal.find('.modal-body #longitude_x').val(button.data('long'));
            modal.find('.modal-body #physical_location_x').val(button.data('physical_location'));
            modal.find('.modal-body #postal_address_x').val(button.data('postal_address'));


        }); //end
    </script>
@endpush
