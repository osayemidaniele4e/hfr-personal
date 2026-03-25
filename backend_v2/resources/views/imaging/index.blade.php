@extends('layouts.master')


@section('content-title')
    Imaging or Radiological Premises

    @if (auth()->user()->hasPermissionTo(14))
        <a href="{{ route('imaging.create') }}">
            <button type="button" class="btn btn-primary pull-right">
                Add Imaging or Radiological Facility
            </button>
        </a>
    @endif
@endsection

@section('content')
    <div class="box box-default collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title">Search</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="box-header with-border">
                <form class="form-horizontal" action="{{ route('imaging.search') }}" method="get">
                    @csrf
                    <div class="form-group">
                        <div class="col-sm-3">

                            <select class="form-control select2" id="state_id" name ="state_id" style="width: 100%;">
                                <option value="1">--Select State--</option>
                                @foreach (getStates() as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach
                            </select>

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
                            <select class="form-control select2" id="ownership_id" name="ownership_id" style="width: 100%;">
                                <option value="0">--Select Ownership--</option>
                                @foreach (getOwnership() as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach

                            </select>
                        </div>

                    </div>
                    <div class="form-group">

                        <div class="col-sm-3">
                            <select class="form-control select2" id="operational_status_id" name ="operational_status_id"
                                style="width: 100%;">
                                <option value="0">--Select Operational Status--</option>
                                @foreach (getLabOperationalStatus() as $st)
                                    <option value="{{ $st->id }}">{{ $st->status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <select class="form-control select2" id="registration_status_id" name="registration_status_id"
                                style="width: 100%;">
                                <option value="0">--Select Registration Status--</option>
                                @foreach (getLabRegistrationStatus() as $st)
                                    <option value="{{ $st->id }}">{{ $st->status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <select class="form-control select2" id="license_status_id" name="license_status_id"
                                style="width: 100%;">
                                <option value="0">--Select License Status--</option>
                                @foreach (getLicenseStatus() as $st)
                                    <option value="{{ $st->id }}">{{ $st->status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <select class="form-control select2" id="geo_codes" name="geo_codes" style="width: 100%;">
                                <option value="0">--Select Coordinates--</option>
                                <option value="1">With Coordinates</option>
                                <option value="2">With No Coordinates</option>
                            </select>
                        </div>

                    </div>
                    <div class="form-group">
                        <div class="col-sm-9">
                            <input class="form-control input-sm"type="text" name="facility_name" id="facility_name"
                                class="form-control" placeholder="Imaging or radiology facility name">
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-sm pull-right btn-block"
                                        id='reset'>Reset</button>
                                </div>
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-success btn-block btn-sm">Search</button>
                                </div>
                            </div>
                        </div>

                    </div>

                </form>
            </div>
        </div>

    </div>

    <div class="box">

        <div class="box-body">

            <table id="table1" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>State</th>
                        <th>LGA</th>
                        <th>Ward</th>
                        <th>Facility Code</th>
                        <th>Facility Name</th>
                        <th>Ownership</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($imagings as $im)
                        <tr>
                            <td>{{ $im->state }}</td>
                            <td>{{ $im->lga }}</td>
                            <td>{{ $im->ward }}</td>
                            <td>{{ $im->unique_id }}</td>
                            <td>{{ $im->facility_name }}</td>
                            <td>{{ $im->ownership }}</td>
                            <td>
                                @if (auth()->user()->hasPermissionTo(13))
                                    <a href="javascript:void(0);">
                                        <button class="btn btn-success btn-sm view-details"
                                            data-id="{{ $im->id }}">View</button>
                                    </a>
                                @endif
                                @if (auth()->user()->hasPermissionTo(15))
                                    <a href="{{ route('imaging.edit', $im->id) }}">
                                        <button class="btn btn-warning btn-sm" type="button">Edit</button>
                                    </a>
                                @endif
                                @if (auth()->user()->hasPermissionTo(16))
                                    <a href="#">
                                        <button class="btn btn-danger btn-sm" data-id="{{ $im->id }}"
                                            data-name="{{ $im->facility_name }}" type="button" data-toggle="modal"
                                            data-target="#deleteModal"> Delete</button>
                                    </a>
                                @endif

                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
        <div class="box-footer">
            <div class="row">

                @php
                    $perpage = $imagings->perpage();
                    $currentpage = $imagings->currentpage();
                    $from = ($currentpage - 1) * $perpage + 1;

                    if ($imagings->currentpage() == $imagings->lastpage()) {
                        $to = $imagings->total();
                    } else {
                        $to = $currentpage * $perpage;
                    }
                @endphp

                <div class="col-md-4">
                    Showing {{ $from }} to {{ $to }} of {{ $imagings->total() }} entries

                </div>
                <div class="col-md-8">
                    <div class="pull-right">
                        {{ $imagings->links() }}
                    </div>
                </div>

            </div>
        </div>
        <!-- /.box-body -->
    </div>
    <div id="dynamicModalContainer"></div>
    <!-- /.box -->
@endsection

@include('imaging.delete')

@push('bk_script')
    @include('partials.dynamic_state_script')
    @include('partials.notification')

    <script>
        $(document).ready(function() {
            $("#state_id").val({{ $state_id }}).change();
            $("#geo_codes").val({{ $geo_codes }}).change();
            $("#facility_name").val("{{ $facility_name }}");
            $("#ownership_id").val({{ $ownership_id }}).change();
            $("#operational_status_id").val({{ $operational_status_id }}).change();
            $("#registration_status_id").val({{ $registration_status_id }}).change();
            $("#license_status_id").val({{ $license_status_id }}).change();

            $("#reset").click(function() {
                $("#geo_codes").val(0).change();
                $("#facility_name").val("");
                $("#ownership_id").val(0).change();
                $("#operational_status_id").val(0).change();
                $("#registration_status_id").val(0).change();
                $("#license_status_id").val(0).change();
            });

            $('#deleteModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget)
                var id = button.data('id')
                var message = "Are you sure you want to delete '".concat(button.data('name'), "' ?");
                var modal = $(this)
                modal.find('.modal-body #message').text(message);
                modal.find('.modal-body #fac_id').val(id)
            })

        });


        $(document).on('click', '.view-details', function() {
            const id = $(this).data('id');
            $.ajax({
                url: `/admin/imaging/show/${id}`, // Adjust route as needed
                type: 'GET',
                success: function(response) {
                    $("#dynamicModalContainer").html(response);
                    $("#imagingModal" + id).modal("show");
                    // $('#pharmacy-details').html(data.html);
                },
                error: function() {
                    // $('#view-details').html('<p class="text-danger">Failed to load details.</p>');
                }
            });
        });
    </script>
@endpush
