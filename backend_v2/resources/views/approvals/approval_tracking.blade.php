@extends('layouts.master')


@section('content-title')
    Facility Approval Tracking
@endsection

@section('content')


    <div class="box">

        <div class="box-body">

            {{-- search option --}}
            <form class="form-horizontal" action="{{ route('approval.trackingsearch') }}" method="GET">
                @csrf

                <div class="form-group">
                    <div class="col-md-3">
                        <select class="form-control select2" id="state_id" name ="state_id" style="width: 100%;">
                            <option value="1">--Select State--</option>
                            @foreach (getStates() as $st)
                                <option value="{{ $st->id }}" {{ $st->id == old('state_id') ? 'selected' : '' }}>
                                    {{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control select2" class="form-control" name="action" data-width="100%">
                            <option value="FACILITY">--Select Request Type--</option>
                            <option value="CREATE FACILITY" {{ 'CREATE FACILITY' == old('action') ? 'selected' : '' }}>
                                Create Facility</option>
                            <option value="UPDATE FACILITY" {{ 'UPDATE FACILITY' == old('action') ? 'selected' : '' }}>
                                Update Facility</option>
                            <option value="DELETE FACILITY" {{ 'DELETE FACILITY' == old('action') ? 'selected' : '' }}>
                                Delete Facility</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select class="form-control select2" class="form-control" name="approval" data-width="100%">
                            <option value="1" {{ '1' == old('approval') ? 'selected' : '' }}>Pending Verifications
                            </option>
                            <option value="2" {{ '2' == old('approval') ? 'selected' : '' }}>Pending Validations
                            </option>
                            <option value="3" {{ '3' == old('approval') ? 'selected' : '' }}>Pending Publications
                            </option>
                        </select>
                    </div>

                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-success pull-right  btn-block btn-sm">Show</button>
                    </div>
                </div>

            </form>
            <hr>
            {{-- --endsearch-- --}}

            <div class="callout callout-success">
                <p>{{ $message }}</p>
            </div>

            @if (!$pending->isEmpty())
                <table id="table1" class="table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Facility Name</th>
                            <th>Request Type</th>
                            <th>Requested By</th>
                            <th>Verification</th>
                            <th>Validation</th>
                            <th>Publication</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pending as $p)
                            <tr>
                                <td>
                                    {{ $p->facility_name }} <br> <br>
                                    <Strong>State: </Strong>{{ $p->state }} <br>
                                    <Strong>LGA: </Strong>{{ $p->lga }} <br>
                                </td>
                                <td><span class="label label-default">{{ $p->action }} </span></td>
                                <td>
                                    <Strong></Strong>{{ $p->requested_by }} <br>
                                    <Strong>E-mail: </Strong>{{ $p->requested_email }} <br>
                                    <Strong>Mobile: </Strong>{{ $p->requested_mobile }} <br>
                                    <Strong>Date:
                                    </Strong>{{ $p->requested_at ? date('d M Y', strtotime($p->requested_at)) : '' }} <br>
                                    @if ($p->request_note != '')
                                        <font color="MediumSeaGreen"><Strong>Remarks: </Strong></font>
                                        {{ $p->request_note }} <br>
                                    @endif
                                </td>
                                <td>
                                    @if ($p->verified_by != '')
                                        @if (in_array($p->status_id, [2, 9, 16, 4, 11, 18, 5, 12, 19, 7, 14, 21]))
                                            <span class="label label-success"> Accepted </span> <br>
                                        @endif
                                        @if (in_array($p->status_id, [3, 10, 17]))
                                            <span class="label label-danger"> Rejected </span> <br>
                                        @endif
                                        <Strong>Date:
                                        </Strong>{{ $p->verified_at ? date('d M Y', strtotime($p->verified_at)) : '' }}
                                        <br>
                                        <Strong>By: </Strong>{{ $p->verified_by }} <br>
                                        <Strong>E-mail: </Strong>{{ $p->verified_email }} <br>
                                        <Strong>Mobile: </Strong>{{ $p->verified_mobile }} <br>
                                        <font color="MediumSeaGreen"><Strong>Remarks: </Strong></font>{{ $p->verify_note }}
                                        <br>
                                    @else
                                        Pending Verification
                                    @endif

                                </td>
                                <td>
                                    @if ($p->validated_by != '')
                                        @if (in_array($p->status_id, [4, 11, 18, 7, 14, 21]))
                                            <span class="label label-success"> Accepted </span> <br>
                                        @endif
                                        @if (in_array($p->status_id, [5, 12, 19]))
                                            <span class="label label-danger"> Rejected </span> <br>
                                        @endif
                                        <Strong>Date:
                                        </Strong>{{ $p->validated_at ? date('d M Y', strtotime($p->validated_at)) : '' }}
                                        <br>
                                        <Strong>By: </Strong>{{ $p->validated_by }} <br>
                                        <Strong>E-mail: </Strong>{{ $p->validated_email }} <br>
                                        <Strong>Mobile: </Strong>{{ $p->validated_mobile }} <br>
                                        <font color="MediumSeaGreen"><Strong>Remarks: </Strong></font>
                                        {{ $p->validate_note }} <br>
                                    @else
                                        Pending Validation
                                    @endif

                                </td>
                                <td>
                                    @if ($p->published_by != '')
                                        @if (in_array($p->status_id, [6, 13, 20]))
                                            <span class="label label-success"> Accepted </span> <br>
                                        @endif
                                        @if (in_array($p->status_id, [7, 14, 21]))
                                            <span class="label label-danger"> Rejected </span> <br>
                                        @endif
                                        <Strong>Date:
                                        </Strong>{{ $p->published_at ? date('d M Y', strtotime($p->published_at)) : '' }}
                                        <br>
                                        <Strong>By: </Strong>{{ $p->published_by }} <br>
                                        <Strong>E-mail: </Strong>{{ $p->published_email }} <br>
                                        <Strong>Mobile: </Strong>{{ $p->published_mobile }} <br>
                                        <font color="MediumSeaGreen"><Strong>Remarks: </Strong></font>
                                        {{ $p->publish_note }} <br>
                                    @else
                                        Pending Publication
                                    @endif
                                </td>




                            </tr>
                        @endforeach

                    </tbody>
                </table>
            @endif
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->





    {{-- modal facility details --}}
    <div class="modal fade" id="view_details" tabindex="-1" role="dialog">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Review facility</h4>
                    <div class='notifications top-right'></div>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('publish.store') }}">
                        @include('approvals.details_modal_body')
                    </form>

                </div><!--modal body ends -->
            </div><!--/.modal-content -->
        </div>
    </div><!--/.modal -->
@endsection


@push('bk_script')
    @include('partials.notification')


    <script>
        $(document).ready(function() {
            $('#table1').DataTable({
                "paging": true,
                "ordering": true,
                "info": true,
                responsive: true
            });
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

            $days = button.data('operational_days');
            $operational_days = $days.replace(/\,/g, ", ");

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
            modal.find('.modal-body #operational_days').text($operational_days);
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
            modal.find('.modal-body #requested_action').val(button.data('action'));
            modal.find('.modal-body #id').val(button.data('id'));

            var hosp_id = button.data('id');
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('hospitals.getServicesHistory') }}",
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
    </script>
@endpush
