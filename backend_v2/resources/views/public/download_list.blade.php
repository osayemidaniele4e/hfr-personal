@extends('layouts.pub.master')

@section('custom_css')
    <link rel="stylesheet" href="{{ asset('dist/jQuery-MultiSelect-master/jquery.multiselect.css') }}">
@endsection


@section('content')
    <div class="latest-area section-padding bg-white">
        <div class="container">

            <div class="box-header">

                <form class="form-horizontal" action="{{ route('download.export') }}" method="GET">
                    @csrf
                    <div class="form-group">
                        <div class="col-sm-4">
                            <select class="form-control select2" id="facility_type_id" name="facility_type_id">
                                @foreach (getFacilityTypes() as $ty)
                                    <option value="{{ $ty->id }}">{{ $ty->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" id="state_id" multiple="multiple" name ="state[]" required>
                                @foreach (getStates() as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-4">
                            <select class="form-control select2" id="lga_id" name="lga_id">
                                <option value="1">--Select LGA--</option>
                            </select>
                        </div>


                    </div>

                    <div class="form-group">
                        <div class="col-sm-4">
                            <select class="form-control select2" id="ward_id" name="ward_id">
                                <option value="0">--Select Ward--</option>
                            </select>
                        </div>


                        <div class="col-sm-4">
                            <select class="form-control select2" id="facility_level_id" name="facility_level_id"
                                style="width: 100%;">
                                <option value="0">--Select Facility Level--</option>
                                @foreach (getLevelOfCare() as $st)
                                    <option value="{{ $st->id }}"
                                        {{ $st->id == $data['facility_level_id'] ? 'selected' : '' }}>{{ $st->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control select2" id="ownership_id" name="ownership_id" style="width: 100%;">
                                <option value="0">--Select Ownership--</option>
                                @foreach (getOwnership() as $st)
                                    <option value="{{ $st->id }}"
                                        {{ $st->id == $data['ownership_id'] ? 'selected' : '' }}>{{ $st->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="form-group">

                        <div class="col-sm-4">
                            <select class="form-control select2" id="operational_status_id" name ="operational_status_id">
                                <option value="0">--Select Operational Status--</option>
                                @foreach (getOperationalStatus() as $st)
                                    <option value="{{ $st->id }}"
                                        {{ $st->id == $data['operational_status_id'] ? 'selected' : '' }}>
                                        {{ $st->status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control select2" id="registration_status_id" name="registration_status_id"
                                style="width: 100%;">
                                <option value="0">--Select Registration Status--</option>
                                @foreach (getRegistrationStatus() as $st)
                                    <option value="{{ $st->id }}"
                                        {{ $st->id == $data['registration_status_id'] ? 'selected' : '' }}>
                                        {{ $st->status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control select2" id="license_status_id" name="license_status_id"
                                style="width: 100%;">
                                <option value="0">--Select License Status--</option>
                                @foreach (getLicenseStatus() as $st)
                                    <option value="{{ $st->id }}"
                                        {{ $st->id == $data['license_status_id'] ? 'selected' : '' }}>{{ $st->status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                    </div>
                    <div class="form-group">
                        <div class="col-sm-4">
                            <select class="form-control select2" id="geo_codes" name="geo_codes">
                                <option value="0">--Select Coordinates--</option>
                                <option value="1" {{ 1 == $data['geo_codes'] ? 'selected' : '' }}>With Coordinates
                                </option>
                                <option value="2" {{ 2 == $data['geo_codes'] ? 'selected' : '' }}>With No
                                    Coordinates</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control select2" id="service_type" name="service_type">
                                <option value="0">--Select Service Type--</option>
                                <option value="1" {{ 1 == $data['service_type'] ? 'selected' : '' }}>Out Patient
                                </option>
                                <option value="2" {{ 2 == $data['service_type'] ? 'selected' : '' }}>In Patient
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-sm pull-right btn-block"
                                        id='reset'>Reset</button>
                                </div>
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-success btn-block btn-sm">Download</button>
                                </div>
                            </div>

                        </div>

                    </div>

                </form>



            </div>

            <div class="box-body">



            </div>
            <!-- /.box-body -->
            <div class="box-footer">

            </div>
        </div> <!-- /contanier-->
    </div> <!-- / -->
@endsection

@push('custom_scripts')
    @include('partials.dynamic_state_script')
    @include('partials.notification')
    <script src="{{ asset('dist/jQuery-MultiSelect-master/jquery.multiselect.js') }}"></script>

    <script>
        $(document).ready(function() {


            $("#reset").click(function() {
                $("#geo_codes").val(0).change();
                $("#state_id").val(1).change();
                $("#facility_type_id").val(1).change();
                $("#facility_name").val("");
                $("#facility_level_id").val(0).change();
                $("#ownership_id").val(0).change();
                $("#operational_status_id").val(0).change();
                $("#registration_status_id").val(0).change();
                $("#license_status_id").val(0).change();
                $("#service_type").val(0).change();
                $("#service_category_id").val(0).change();
                $("#services").val(0).change();
            });



            $('#state_id').multiselect({
                columns: 4,
                search: true,
                selectAll: true,
                texts: {
                    placeholder: 'Select States',
                    search: 'Search States'
                }
            });



        });
    </script>
@endpush
