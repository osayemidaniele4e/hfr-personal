@extends('layouts.master')

@section('content-title')
    Facility Verification
@endsection

@section('content')

    <div class="box">

        <div class="box-body">
            {{-- search option --}}
            <form class="form-horizontal" action="{{ route('verify.search') }}" method="GET">
                @csrf

                <div class="form-group">
                    <div class="col-md-5">
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
                    <div class="col-md-5">
                        <select class="form-control select2" class="form-control" name="status" data-width="100%">
                            <option value="1" {{ '1' == old('status') ? 'selected' : '' }}>My Pending Verifications
                            </option>
                            <option value="2" {{ '2' == old('status') ? 'selected' : '' }}>My Accepted Verifications
                                (Pending Validation)</option>
                            <option value="3" {{ '3' == old('status') ? 'selected' : '' }}>My Rejected Verifications
                            </option>
                            <option value="4" {{ '4' == old('status') ? 'selected' : '' }}>My Verifications</option>
                        </select>
                    </div>

                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-success pull-right  btn-block btn-sm">Show</button>
                    </div>
                </div>

            </form>
            <hr>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pending as $p)
                            <tr>
                                <td>
                                    {{ $p->facility_name }} <br> <br>
                                    <Strong>LGA: </Strong>{{ $p->lga }} <br>
                                </td>
                                <td><span class="label label-default">{{ $p->action ?? 'N/A' }} </span></td>
                                <td>
                                    <Strong></Strong>{{ $p->requested_by_lastname }} <br>
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
                                    @if ($p->verified_email ?? '' == '' or in_array($p->status_id, [5, 12, 19]))
                                        Pending Verification
                                    @else
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
                                    @endif

                                </td>
                                <td>
                                    @if ($p->validated_email ?? '' != '')
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
                                    @if ($p->published_by ?? '' != '')
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
                                        <Strong>E-mail: </Strong>{{ $p->published_email ?? "N/A" }} <br>
                                        <Strong>Mobile: </Strong>{{ $p->published_mobile ?? "N/A"}} <br>
                                        <font color="MediumSeaGreen"><Strong>Remarks: </Strong>></font>
                                        {{ $p->publish_note }} <br>
                                    @else
                                        Pending Publication
                                    @endif

                                </td>
                                <td>
                                    @if (in_array($p->status_id, [1, 5, 8, 12, 15, 19]))
                                        @if ($p->action === 'CREATE FACILITY')
                                            <a href="#">
                                                <button class="btn btn-success btn-sm" type="button" data-toggle="modal"
                                                    data-target="#view_details" data-id="{{ $p->id }}"
                                                    data-unique_id="{{ $p->unique_id }}"
                                                    data-registration_no="{{ $p->registration_no }}"
                                                    data-start_date="{{ $p->start_date }}"
                                                    data-facility_name="{{ $p->facility_name }}"
                                                    data-alt_facility_name="{{ $p->alt_facility_name }}"
                                                    data-state="{{ $p->state }}" data-lga="{{ $p->lga }}"
                                                    data-ward="{{ $p->ward }}" data-ownership="{{ $p->ownership }}"
                                                    data-ownership_type="{{ $p->ownership_type ?? "N/A"}}"
                                                    data-facility_level="{{ $p->facility_level ?? "N/A"}}"
                                                    data-facility_level_option="{{ $p->facility_level_option ?? "N/A"}}"
                                                    data-physical_location="{{ $p->physical_location }}"
                                                    data-alternate_number="{{ $p->alternate_number ?? "N/A"}}"
                                                    data-longitude="{{ $p->longitude }}"
                                                    data-latitude="{{ $p->latitude }}"
                                                    data-postal_address="{{ $p->postal_address }}"
                                                    data-phone_number="{{ $p->phone_number }}"
                                                    data-email_address="{{ $p->email_address }}"
                                                    data-website="{{ $p->website }}"
                                                    data-operational_days="{{ $p->operational_days }}"
                                                    data-operational_hours="{{ $p->operational_hours }}"
                                                    data-operation_status="{{ $p->operation_status ?? "N/A" }}"
                                                    data-registration_status="{{ $p->registration_status ?? "N/A" }}"
                                                    data-license_status="{{ $p->license_status ?? "N/A" }}"
                                                    data-doctors="{{ $p->doctors }}"
                                                    data-pharmacists="{{ $p->pharmacists }}"
                                                    data-dentist="{{ $p->dentist }}"
                                                    data-pharmacy_technicians="{{ $p->pharmacy_technicians }}"
                                                    data-nurses="{{ $p->nurses }}"
                                                    data-lab_scientists="{{ $p->lab_scientists }}"
                                                    data-midwifes="{{ $p->midwifes }}"
                                                    data-lab_technicians="{{ $p->lab_technicians }}"
                                                    data-nurse_midwife="{{ $p->nurse_midwife }}"
                                                    data-him_officers="{{ $p->him_officers }}"
                                                    data-community_health_officer="{{ $p->community_health_officer }}"
                                                    data-community_extension_workers="{{ $p->community_extension_workers }}"
                                                    data-jun_community_extension_worker="{{ $p->jun_community_extension_worker }}"
                                                    data-dental_technicians="{{ $p->dental_technicians }}"
                                                    data-env_health_officers="{{ $p->env_health_officers }}"
                                                    data-inpatient="{{ $p->inpatient }}"
                                                    data-outpatient="{{ $p->outpatient }}" data-beds="{{ $p->beds }}"
                                                    data-onsite_laboratory="{{ $p->onsite_laboratory }}"
                                                    data-onsite_imaging="{{ $p->onsite_imaging }}"
                                                    data-onsite_pharmarcy="{{ $p->onsite_pharmarcy }}"
                                                    data-mortuary_services="{{ $p->mortuary_services }}"
                                                    data-attendants = "{{ $p->attendants }}"
                                                    data-ambulance_services="{{ $p->ambulance_services }}"
                                                    data-state_unique_id="{{ $p->state_unique_id }}"
                                                    data-outpatient = "{{ $p->outpatient }}"
                                                    data-inpatient="{{ $p->inpatient }}"
                                                    data-action="{{ $p->action }}">
                                                    Review
                                                </button>
                                            </a>
                                        @elseif ($p->action === 'UPDATE FACILITY')
                                            <a
                                                href="{{ route('view.updated_records', ['id' => $p->id, 'stage' => '1']) }}">
                                                <button class="btn btn-success btn-sm" type="button"> Review</button>
                                            </a>
                                        @else
                                            <a href="#">
                                                <button class="btn btn-success btn-sm" type="button" data-toggle="modal"
                                                    data-target="#view_details" data-id="{{ $p->id }}"
                                                    data-unique_id="{{ $p->unique_id }}"
                                                    data-registration_no="{{ $p->registration_no }}"
                                                    data-start_date="{{ $p->start_date }}"
                                                    data-facility_name="{{ $p->facility_name }}"
                                                    data-alt_facility_name="{{ $p->alt_facility_name }}"
                                                    data-state="{{ $p->state }}" data-lga="{{ $p->lga }}"
                                                    data-ward="{{ $p->ward }}" data-ownership="{{ $p->ownership }}"
                                                    data-ownership_type="{{ $p->ownership_type ?? "N/A" }}"
                                                    data-facility_level="{{ $p->facility_level ?? "N/A" }}"
                                                    data-facility_level_option="{{ $p->facility_level_option ?? "N/A" }}"
                                                    data-physical_location="{{ $p->physical_location }}"
                                                    data-alternate_number="{{ $p->alternate_number ?? "N/A" }}"
                                                    data-longitude="{{ $p->longitude }}"
                                                    data-latitude="{{ $p->latitude }}"
                                                    data-postal_address="{{ $p->postal_address }}"
                                                    data-phone_number="{{ $p->phone_number }}"
                                                    data-email_address="{{ $p->email_address }}"
                                                    data-website="{{ $p->website }}"
                                                    data-operational_days="{{ $p->operational_days }}"
                                                    data-operational_hours="{{ $p->operational_hours }}"
                                                    data-operation_status="{{ $p->operation_status ?? "N/A" }}"
                                                    data-registration_status="{{ $p->registration_status ?? "N/A" }}"
                                                    data-license_status="{{ $p->license_status ?? "N/A" }}"
                                                    data-doctors="{{ $p->doctors }}"
                                                    data-pharmacists="{{ $p->pharmacists }}"
                                                    data-dentist="{{ $p->dentist }}"
                                                    data-pharmacy_technicians="{{ $p->pharmacy_technicians }}"
                                                    data-nurses="{{ $p->nurses }}"
                                                    data-lab_scientists="{{ $p->lab_scientists }}"
                                                    data-midwifes="{{ $p->midwifes }}"
                                                    data-lab_technicians="{{ $p->lab_technicians }}"
                                                    data-nurse_midwife="{{ $p->nurse_midwife }}"
                                                    data-him_officers="{{ $p->him_officers }}"
                                                    data-community_health_officer="{{ $p->community_health_officer }}"
                                                    data-community_extension_workers="{{ $p->community_extension_workers }}"
                                                    data-jun_community_extension_worker="{{ $p->jun_community_extension_worker }}"
                                                    data-dental_technicians="{{ $p->dental_technicians }}"
                                                    data-env_health_officers="{{ $p->env_health_officers }}"
                                                    data-inpatient="{{ $p->inpatient }}"
                                                    data-outpatient="{{ $p->outpatient }}"
                                                    data-beds="{{ $p->beds }}"
                                                    data-onsite_laboratory="{{ $p->onsite_laboratory }}"
                                                    data-onsite_imaging="{{ $p->onsite_imaging }}"
                                                    data-onsite_pharmarcy="{{ $p->onsite_pharmarcy }}"
                                                    data-mortuary_services="{{ $p->mortuary_services }}"
                                                    data-attendants = "{{ $p->attendants }}"
                                                    data-ambulance_services="{{ $p->ambulance_services }}"
                                                    data-state_unique_id="{{ $p->state_unique_id }}"
                                                    data-outpatient = "{{ $p->outpatient }}"
                                                    data-inpatient="{{ $p->inpatient }}"
                                                    data-action="{{ $p->action }}">
                                                    Review
                                                </button>
                                            </a>
                                        @endif
                                    @endif

                                    @if (in_array($p->status_id, [2, 9, 16]))
                                        <a href="#">
                                            <button class="btn btn-primary btn-sm" type="button" data-toggle="modal"
                                                data-target="#recall" data-id="{{ $p->id }}"
                                                data-action="{{ $p->action }}">
                                                Recall
                                            </button>
                                        </a>
                                    @endif

                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            @else
                <div class="callout callout-success">
                    <p>No record found!</p>
                </div>
            @endif
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->

    {{-- modal facility details for new Facility  --}}
    <div class="modal fade" id="view_details" tabindex="-1" role="dialog">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Review Request</h4>
                    <div class='notifications top-right'></div>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('verify.store') }}">
                        <input type="hidden" id="validated_by" name="validated_by">
                        <input type="hidden" id="validated_at" name="validated_at">
                        <input type="hidden" id="validate_note" name="validate_note">
                        <input type="hidden" id="published_by" name="published_by">
                        <input type="hidden" id="published_at" name="published_at">
                        <input type="hidden" id="publish_note" name="publish_note">

                        @include('approvals.details_modal_body')

                    </form>

                </div><!--modal body ends -->
            </div><!--/.modal-content -->
        </div>
    </div> <!--/.modal -->

    <!-- Modal Recall Verification -->
    <div class="modal fade" id="recall" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">

                <form action="{{ route('verify.recall') }}" method="POST">
                    @csrf

                    <div class="modal-body">
                        <p class="text-center">
                            Are you sure you want to recall verification for this facility?
                        </p>
                        <input type="hidden" id="hosp_id" name="hosp_id">
                        <input type="hidden" id="action" name="action">
                        <input type="hidden" id="verified_by" name="verified_by">
                        <input type="hidden" id="verified_at" name="verified_at">
                        <input type="hidden" id="verified_note" name="verified_note">


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-warning btn-sm">Yes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

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
    <script>
        //recall modal
        $('#recall').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)

            var modal = $(this)
            modal.find('.modal-body #hosp_id').val(button.data('id'));
            modal.find('.modal-body #action').val(button.data('action'));
        }) //end
    </script>
@endpush
