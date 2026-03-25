@csrf
<input type="hidden" id="id" name="id">
<input type="hidden" id="requested_action" name="requested_action">

<div class="panel-body">

    <div class="panel-group" id="accordion">
        {{-- panel one --}}
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Identifiers</a>
                </h4>
            </div>
            <div id="collapse1" class="panel-collapse collapse in">
                <div class="panel-body">
                    <div class="row">
                        <label class="col-md-4">Facility Code:</label>
                        <div class="col-md-8" id="unique_id"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-4">State Unique ID:</label>
                        <div class="col-md-8" id="state_unique_id"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-4">Registration No:</label>
                        <div class="col-md-8" id="registration_no"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-4 text-md-right">Facility Name:</label>
                        <div class="col-md-8" id="facility_name"> </div>
                    </div>
                    <div class="row">
                        <label class="col-md-4 text-md-right">Alternate Name:</label>
                        <div class="col-md-8" id="alt_facility_name"> </div>
                    </div>
                    <div class="row">
                        <label class="col-md-4 text-md-right">Start Date:</label>
                        <div class="col-md-8" id="start_date"> </div>
                    </div>
                    <div class="row">
                        <label class="col-md-4 text-md-right">Ownership:</label>
                        <div class="col-md-8" id="ownership"> </div>
                    </div>
                    <div class="row">
                        <label class="col-md-4 text-md-right">Ownership Type:</label>
                        <div class="col-md-8" id="ownership_type"> </div>
                    </div>

                    <div class="row">
                        <label class="col-md-4 text-md-right">Facility Level:</label>
                        <div class="col-md-8" id="facility_level"> </div>
                    </div>
                    <div class="row">
                        <label class="col-md-4 text-md-right">Facility Level Option:</label>
                        <div class="col-md-8" id="facility_level_option"> </div>
                    </div>
                    <div class="row">
                        <label class="col-md-4 text-md-right">Days of Operation:</label>
                        <div class="col-md-8" id="operational_days"> </div>
                    </div>
                    <div class="row">
                        <label class="col-md-4 text-md-right">Hours of Operation:</label>
                        <div class="col-md-8" id="operational_hours"> </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- panel two --}}
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Location</a>
                </h4>
            </div>
            <div id="collapse2" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="row">
                        <label class="col-md-4">State:</label>
                        <div class="col-md-8" id="state"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-4">LGA:</label>
                        <div class="col-md-8" id="lga"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-4">Ward:</label>
                        <div class="col-md-8" id="ward"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-4"> Physical Location:</label>
                        <div class="col-md-8" id="physical_location"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-4">Postal Address:</label>
                        <div class="col-md-8" id="postal_address"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-4">Longitude:</label>
                        <div class="col-md-8" id="longitude"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-4">Latitude:</label>
                        <div class="col-md-8" id="latitude"></div>
                    </div>

                </div>
            </div>
        </div>
        {{-- panel 3 --}}
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">Contacts</a>
                </h4>
            </div>
            <div id="collapse3" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="row">
                        <label class="col-md-4">Phone Number:</label>
                        <div class="col-md-8" id="phone_number"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-4">Alternate Number:</label>
                        <div class="col-md-8" id="alternate_number"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-4">Email Address:</label>
                        <div class="col-md-8" id="email_address"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-4">Website:</label>
                        <div class="col-md-8" id="website"></div>
                    </div>
                </div>
            </div>
        </div>
        {{-- panel four --}}
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">Status</a>
                </h4>
            </div>
            <div id="collapse4" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="row">
                        <label class="col-md-4">Operational Status:</label>
                        <div class="col-md-8" id="operation_status"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-4">Registration Status:</label>
                        <div class="col-md-8" id="registration_status"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-4">License Status:</label>
                        <div class="col-md-8" id="license_status"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- pane six Services --}}
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse6">Services</a>
                </h4>
            </div>
            <div id="collapse6" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="row">
                        <label class="col-md-6">Out Patient Services:</label>
                        <div class="col-md-6" id="outpatient"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-6">In Patient Services:</label>
                        <div class="col-md-6" id="inpatient"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-6">Medical Services:</label>
                        <div class="col-md-6" id="medical"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-6">Surgical Services:</label>
                        <div class="col-md-6" id="surgical"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-6">Obsterics and Gynecology Services:</label>
                        <div class="col-md-6" id="gyn"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-6">Pediatrics Services:</label>
                        <div class="col-md-6" id="pediatrics"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-6">Dental Services:</label>
                        <div class="col-md-6" id="dental"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-6">Specific Clinical Services:</label>
                        <div class="col-md-6" id="specialservice"></div>
                    </div>

                    <div class="row">
                        <label class="col-md-6">Total number of Beds:</label>
                        <div class="col-md-6" id="beds"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-6">Onsite Laboratory:</label>
                        <div class="col-md-6" id="onsite_laboratory"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-6">Onsite Imaging:</label>
                        <div class="col-md-6" id="onsite_imaging"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-6">Onsite Pharmacy:</label>
                        <div class="col-md-6" id="onsite_pharmarcy"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-6">Mortuary Services:</label>
                        <div class="col-md-6" id="mortuary_services"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-6">Ambulance Services:</label>
                        <div class="col-md-6" id="ambulance_services"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- panel five HFR --}}
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse5">Personnel</a>
                </h4>
            </div>
            <div id="collapse5" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="row">
                        <label class="col-md-10">Number of Doctors:</label>
                        <div class="col-md-2" id="doctors"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-10">Number of Pharmacists:</label>
                        <div class="col-md-2" id="pharmacists"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-10">Number Pharmacy Technicians:</label>
                        <div class="col-md-2" id="pharmacy_technicians"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-10">Number of Dentists:</label>
                        <div class="col-md-2" id="dentist"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-10">Number of Dental Technicians:</label>
                        <div class="col-md-2" id="dental_technicians"></div>
                    </div>

                    <div class="row">
                        <label class="col-md-10">Number of Nurses:</label>
                        <div class="col-md-2" id="nurses"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-10">Number of Midwifes:</label>
                        <div class="col-md-2" id="midwifes"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-10">Number of Nurses/Midwifes:</label>
                        <div class="col-md-2" id="nurse_midwife"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-10">Number of Lab Technicians:</label>
                        <div class="col-md-2" id="lab_technicians"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-10">Number of Lab Scientits:</label>
                        <div class="col-md-2" id="lab_scientists"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-8">Health Records/HIM Officers:</label>
                        <div class="col-md-2" id="him_officers"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-10">Number of Community Health Officer:</label>
                        <div class="col-md-2" id="community_health_officer"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-10">Number of Community Health Extension Worker:</label>
                        <div class="col-md-2" id="community_extension_workers"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-10">Number of Junior Com Health Extension Worker:</label>
                        <div class="col-md-2" id="jun_community_extension_worker"></div>
                    </div>

                    <div class="row">
                        <label class="col-md-10">Number of Environmental Health Officers:</label>
                        <div class="col-md-2" id="env_health_officers"></div>
                    </div>
                    <div class="row">
                        <label class="col-md-10">Number of Health Attendant/Assistant:</label>
                        <div class="col-md-2" id="attendants"></div>
                    </div>
                </div>
            </div>
        </div>
        {{-- panel seven --}}
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse7">Remarks</a>
                </h4>
            </div>
            <div id="collapse7" class="panel-collapse">
                <div class="panel-body">
                    <div class="row">
                        <label class="col-md-12">Verification/ Rejection Note:<font color="red">*</font></label>
                        <div class="col-md-12">
                            <textarea class="form-control" rows="3" name="notes" placeholder="Please enter note ..." required></textarea>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


</div>

<div class="modal-footer">
    <button type="submit" class="btn btn-danger" name="action" value="reject">Reject</button>
    <button type="submit" class="btn btn-success" name="action" value="approve">Accept</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
