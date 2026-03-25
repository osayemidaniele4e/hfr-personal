<div class="modal fade" id="laboratoryModal{{ $laboratory->id }}" tabindex="-1" aria-labelledby="pharmacyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pharmacyModalLabel">Laboratory Details</h5>
            </div>
            <div class="modal-body">

                <div class="panel-body">

                    <div class="panel-group" id="accordion">
                        {{-- panel one --}}
                        <div class="panel panel-default">
                            {{-- <div class="panel-heading">
                                 <h4 class="panel-title">
                                     <a data-toggle="collapse" data-parent="#accordion"
                                         href="#collapse1">Identifiers</a>
                                 </h4>
                             </div> --}}
                            <div id="collapse1" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <div class="row">
                                        <label class="col-md-4">Facility Code:</label>
                                        <div class="col-md-8" id="">{{ $laboratory->unique_id ?? '' }}</div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4">Registration No:</label>
                                        <div class="col-md-8" id="registration_no">
                                            {{ $laboratory->registration_no ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4"> Laboratory No:</label>
                                        <div class="col-md-8" id="pharmacists_reg_number">
                                            {{ $laboratory->medical_laboratory_number ?? '' }}
                                        </div>
                                    </div>


                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Laboratory Name:</label>
                                        <div class="col-md-8" id="facility_name">
                                            {{ $laboratory->facility_name ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Laboratory Name:</label>
                                        <div class="col-md-8" id="alt_facility_name">
                                            {{ $laboratory->alt_facility_name ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Phone Number:</label>
                                        <div class="col-md-8" id="phone_number">
                                            {{ $laboratory->phone_number ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Email:</label>
                                        <div class="col-md-8" id="email_address">
                                            {{ $laboratory->email_address ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Address:</label>
                                        <div class="col-md-8" id="address">
                                            {{ $laboratory->postal_address ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Start Date:</label>
                                        <div class="col-md-8" id="start_date">
                                            {{ $laboratory->start_date ?? '' }} </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Ownership:</label>
                                        <div class="col-md-8" id="ownership">
                                            {{ $laboratory->ownership ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Ownership Type:</label>
                                        <div class="col-md-8" id="ownership_type">
                                            {{ $laboratory->ownership_type ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">State:</label>
                                        <div class="col-md-8" id="state">
                                            {{ $laboratory->state ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">LGA:</label>
                                        <div class="col-md-8" id="lga">
                                            {{ $laboratory->lga ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Ward:</label>
                                        <div class="col-md-8" id="ward">
                                            {{ $laboratory->ward ?? '' }}
                                        </div>
                                    </div>


                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Longitude:</label>
                                        <div class="col-md-8" id="longitude">
                                            {{ $laboratory->longitude ?? '' }}
                                        </div>
                                    </div>



                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Latitude:</label>
                                        <div class="col-md-8" id="latitude">
                                            {{ $laboratory->latitude ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Laboratory Scientists:</label>
                                        <div class="col-md-8" id="facility_level_option">
                                            {{ $laboratory->laboratory_scientists ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Laboratory Technicians:</label>
                                        <div class="col-md-8" id="pharmacy_technicians">
                                            {{ $laboratory->laboratory_technicians ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Days of Operatsion:</label>
                                        <div class="col-md-8" id="operational_days">
                                            @if ($laboratory->operational_days)
                                                @foreach (explode(',', $laboratory->operational_days) as $day)
                                                    <span class="badge badge-primary">{{ trim($day) }}</span>
                                                @endforeach
                                            @else
                                                <span class="badge badge-info">Not specified</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Hours of Operation:</label>
                                        <div class="col-md-8" id="operational_hours">
                                            {{ $laboratory->operational_hours ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!--/.modal-dialog -->
    </div>
</div><!--/.modal -->
