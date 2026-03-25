<div class="modal fade" id="pharmacyModal{{ $pharmacy->id }}" tabindex="-1" aria-labelledby="pharmacyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pharmacyModalLabel">Pharmaceutical Details</h5>
                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
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
                                        <div class="col-md-8" id="">{{ $pharmacy->unique_id ?? '' }}</div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4">Registration No:</label>
                                        <div class="col-md-8" id="registration_no">
                                            {{ $pharmacy->registration_no ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4"> Pharmacists No:</label>
                                        <div class="col-md-8" id="pharmacists_reg_number">
                                            {{ $pharmacy->pharmacists_reg_number ?? '' }}
                                        </div>
                                    </div>


                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Facility Name:</label>
                                        <div class="col-md-8" id="facility_name">
                                            {{ $pharmacy->facility_name ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Alternate Name:</label>
                                        <div class="col-md-8" id="alt_facility_name">
                                            {{ $pharmacy->alt_facility_name ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Phone Number:</label>
                                        <div class="col-md-8" id="phone_number">
                                            {{ $pharmacy->phone_number ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Email:</label>
                                        <div class="col-md-8" id="email_address">
                                            {{ $pharmacy->email_address ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Address:</label>
                                        <div class="col-md-8" id="address">
                                            {{ $pharmacy->postal_address ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Start Date:</label>
                                        <div class="col-md-8" id="start_date">
                                            {{ $pharmacy->start_date ?? '' }} </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Ownership:</label>
                                        <div class="col-md-8" id="ownership">
                                            {{ $pharmacy->ownership ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Ownership Type:</label>
                                        <div class="col-md-8" id="ownership_type">
                                            {{ $pharmacy->ownership_type ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">State:</label>
                                        <div class="col-md-8" id="state">
                                            {{ $pharmacy->state ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">LGA:</label>
                                        <div class="col-md-8" id="lga">
                                            {{ $pharmacy->lga ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Ward:</label>
                                        <div class="col-md-8" id="ward">
                                            {{ $pharmacy->ward ?? '' }}
                                        </div>
                                    </div>


                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Longitude:</label>
                                        <div class="col-md-8" id="longitude">
                                            {{ $pharmacy->longitude ?? '' }}
                                        </div>
                                    </div>



                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Latitude:</label>
                                        <div class="col-md-8" id="latitude">
                                            {{ $pharmacy->latitude ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Pharmacists:</label>
                                        <div class="col-md-8" id="facility_level_option">
                                            {{ $pharmacy->pharmacists ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Pharmacy Technicians:</label>
                                        <div class="col-md-8" id="pharmacy_technicians">
                                            {{ $pharmacy->pharmacy_technicians ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Days of Operatsion:</label>
                                        <div class="col-md-8" id="operational_days">
                                            @if ($pharmacy->operational_days)
                                                @foreach (explode(',', $pharmacy->operational_days) as $day)
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
                                            {{ $pharmacy->operational_hours ?? '' }}
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
