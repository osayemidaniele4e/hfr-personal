@extends('layouts.master')


@section('content-title')
    Facility Services Rendered
@endsection

@section('content')


    <div class="box">

        <div class="box-body">
            <form class="form-horizontal" action="{{ route('services.report') }}" method="GET">
                @csrf

                <div class="form-group">
                    <div class="col-sm-4">
                        <select class="form-control select2" id="state_id" name ="state_id">
                            <option value="1">All States</option>
                            @foreach (getStates() as $st)
                                <option value="{{ $st->id }}" {{ $st->id == $data['state_id'] ? 'selected' : '' }}>
                                    {{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-4">
                        <select class="form-control select2" id="lga_id" name="lga_id">
                            <option value="1">--Select LGA--</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select class="form-control select2" id="ward_id" name="ward_id">
                            <option value="1">--Select Ward--</option>
                        </select>
                    </div>

                </div>
                <div class="form-group">
                    
                    <div class="col-sm-4">
                        <select class="form-control select2" id="facility_level_id" name="facility_level_id"
                            style="width: 100%;">
                            <option value="">--Select Facility Level--</option>
                            @foreach (getLevelOfCare() as $st)
                                <option value="{{ $st->id }}"
                                    {{ $st->id == $data['facility_level_id'] ? 'selected' : '' }}>{{ $st->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-4">
                        <select class="form-control select2" id="ownership_id" name="ownership_id" style="width: 100%;">
                            <option value="">--Select Ownership--</option>
                            @foreach (getOwnership() as $st)
                                <option value="{{ $st->id }}"
                                    {{ $st->id == $data['ownership_id'] ? 'selected' : '' }}>{{ $st->name }}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="col-sm-2"> </div>
                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-success btn-block pull-right  btn-sm">Show</button>
                    </div>
                </div>
                <hr>
            </form>

            @if ($facilities->count() == 0)
                <div class="alert alert-success alert-dismissible">
                    No record found!
                </div>
            @else
                <table id="table21" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>State</th>
                            <th>LGA</th>
                            <th>Ward</th>
                            <th>Facility Name</th>
                            <th>Service Rendered</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($facilities as $fac)
                            <tr>
                                <td>{{ $fac->state }}</td>
                                <td>{{ $fac->lga }}</td>
                                <td>{{ $fac->ward }}</td>
                                <td>{{ $fac->facility_name }}</td>
                                <td>
                                    @if (!empty($fac->services))
                                        <ul class="list-disc pl-4">
                                            @foreach (explode(',', $fac->services) as $service)
                                                <li>{{ trim($service) }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-400">No services</span>
                                    @endif
                                </td>

                            </tr>
                        @endforeach

                    </tbody>
                </table>
            @endif


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
                <form class="form-horizontal" action="{{ route('services.download') }}" method="post">
                    @csrf

                    <button type="submit" class="btn btn-primary btn-sm" name='format' value='xls'>Download</button>

                </form>
            </div>


        </div>

    </div>
    <!-- /.box -->


@endsection


@push('bk_script')
    @include('partials.notification')
    @include('partials.dynamic_state_script')

    <script>
        $(document).ready(function() {
            $('#table2').DataTable({
                "paging": true,
                "ordering": true,
                "info": true
            });

        });
    </script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();
            let _token = $('input[name="_token"]').val();

            @if (Auth::user()->state_id != 1)
                // Automatically fetch LGA for non-admin users based on their state
                let stateID = {{ Auth::user()->state_id }};
                $.ajax({
                    url: "{{ route('getLgaList') }}",
                    method: "POST",
                    data: {
                        id: stateID,
                        _token: _token
                    },
                    success: function(result) {
                        $('#lga_id').html(result);
                        $('#lga_id').val("{{ old('lga_id') }}").trigger('change');
                    }
                });
            @else
                // For admin (state_id == 1), check if a state was previously selected (after Show clicked)
                let selectedStateId = $('#state_id').val();
                if (selectedStateId && selectedStateId != 1) {
                    $.ajax({
                        url: "{{ route('getLgaList') }}",
                        method: "POST",
                        data: {
                            id: selectedStateId,
                            _token: _token
                        },
                        success: function(result) {
                            $('#lga_id').html(result);
                            $('#lga_id').val("{{ old('lga_id') }}").trigger('change');
                        }
                    });
                }

                // If admin changes the state dropdown manually
                $('#state_id').on('change', function() {
                    let stateID = $(this).val();
                    if (stateID && stateID != 1) {
                        $.ajax({
                            url: "{{ route('getLgaList') }}",
                            method: "POST",
                            data: {
                                id: stateID,
                                _token: _token
                            },
                            success: function(result) {
                                $('#lga_id').html(result);
                            }
                        });
                    }
                });
            @endif
        });
    </script>
@endpush
