<div class="modal fade" id="imagingModal{{ $imagings->id }}" tabindex="-1" aria-labelledby="pharmacyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pharmacyModalLabel">Imaging or Radiological Details</h5>
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
                                        <div class="col-md-8" id="">{{ $imagings->unique_id ?? '' }}</div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4">Registration No:</label>
                                        <div class="col-md-8" id="registration_no">
                                            {{ $imagings->registration_no ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4"> Radiographers Reg No:</label>
                                        <div class="col-md-8" id="radiographers_reg_number">
                                            {{ $imagings->radiographers_reg_number ?? '' }}
                                        </div>
                                    </div>


                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Radiographers Name:</label>
                                        <div class="col-md-8" id="facility_name">
                                            {{ $imagings->facility_name ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Radiographers Alternative Name:</label>
                                        <div class="col-md-8" id="alt_facility_name">
                                            {{ $imagings->alt_facility_name ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Phone Number:</label>
                                        <div class="col-md-8" id="phone_number">
                                            {{ $imagings->phone_number ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Email:</label>
                                        <div class="col-md-8" id="email_address">
                                            {{ $imagings->email_address ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Address:</label>
                                        <div class="col-md-8" id="address">
                                            {{ $imagings->postal_address ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Start Date:</label>
                                        <div class="col-md-8" id="start_date">
                                            {{ $imagings->start_date ?? '' }} </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Ownership:</label>
                                        <div class="col-md-8" id="ownership">
                                            {{ $imagings->ownership ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Ownership Type:</label>
                                        <div class="col-md-8" id="ownership_type">
                                            {{ $imagings->ownership_type ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">State:</label>
                                        <div class="col-md-8" id="state">
                                            {{ $imagings->state ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">LGA:</label>
                                        <div class="col-md-8" id="lga">
                                            {{ $imagings->lga ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Ward:</label>
                                        <div class="col-md-8" id="ward">
                                            {{ $imagings->ward ?? '' }}
                                        </div>
                                    </div>


                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Longitude:</label>
                                        <div class="col-md-8" id="longitude">
                                            {{ $imagings->longitude ?? '' }}
                                        </div>
                                    </div>



                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Latitude:</label>
                                        <div class="col-md-8" id="latitude">
                                            {{ $imagings->latitude ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Radiologists:</label>
                                        <div class="col-md-8" id="radiologists">
                                            {{ $imagings->radiologists ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Radiography Technicians:</label>
                                        <div class="col-md-8" id="radiography_tech">
                                            {{ $imagings->radiography_tech ?? '' }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-4 text-md-right">Days of Operatsion:</label>
                                        <div class="col-md-8" id="operational_days">
                                            @if ($imagings->operational_days)
                                                @foreach (explode(',', $imagings->operational_days) as $day)
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
                                            {{ $imagings->operational_hours ?? '' }}
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
