@extends("layouts.master")


@section("bk_css")
    <link rel="stylesheet" href="{{asset("dist/iCheck/minimal/green.css")}}"/>
@endsection 

@section("content")


<form class="form-horizontal" action="{{route('laboratory.update',$labs->id)}}" method="POST">
    @csrf
    @method("PUT")
    
    
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        {{-- Tab One   --}}
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingOne">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Signature Elements
                    </a>
                </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                    <div class="box-body">
                        
                        <div class="form-group">
                            <label for="cac_reg" class="col-sm-2 control-label">Registration No:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="registration_no" name="registration_no" value="{{ $labs->registration_no }}" placeholder="Corporate Affairs Registration Number">
                            </div>
                            
                            <label class="col-sm-2 control-label">Commencement Date:</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="start_date" name="start_date" value="{{$labs->start_date}}" autocomplete="off">
                                    
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="reg_fac_name" class="col-sm-2 control-label">Registered Name: <font color="red">*</font> </label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="facility_name"  name="facility_name" value="{{ $labs->facility_name }}" placeholder="Registered Facility Name">
                            </div>
                            
                            <label for="alt_facility_name" class="col-sm-2 control-label">Alternate Name:</label> 
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="alt_facility_name" name="alt_facility_name" value="{{ $labs->alt_facility_name }}" placeholder="Alternate Facility Name">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label">State:<font color="red">*</font> </label></label>
                            <div class="col-sm-4">
                                <select class="form-control select2 dynamic" id="state_id" name ="state_id" data-dependent="lga_id" required>
                                    <option value="">--Choose one--</option>
                                    @foreach(getStates() as $st)
                                    <option value="{{$st->id}}">{{$st->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <label class="col-sm-2 control-label">LGA:<font color="red">*</font> </label></label>
                            <div class="col-sm-4">
                                <select class="form-control select2 dynamic" id="lga_id" name="lga_id" data-dependent="ward_id" required>
                                    <option value="">--Select LGA--</option>
                                </select>
                            </div>
                            
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Ward:</label>
                            <div class="col-sm-10">
                                <select class="form-control select2" id="ward_id" name="ward_id" style="width: 100%;" required>
                                    <option value="">--Select Ward--</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="house_no" class="col-sm-2 control-label">House Number:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="house_no" name="house_no" value="{{ $labs->house_no }}">
                            </div>
                            
                            <label for="street_name" class="col-sm-2 control-label">Street Name:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="street_name"  name="street_name" value="{{ $labs->street_name }}">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="latitude" class="col-sm-2 control-label">Latitude:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="latitude" name="latitude"  value="{{ $labs->latitide }}">
                            </div>
                            
                            <label for="longitude" class="col-sm-2 control-label">Longitude:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="longitude" name="longitude" value="{{ $labs->longitude }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="postal_address" class="col-sm-2 control-label">Postal Address:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="postal_address"  name="postal_address" value="{{ $labs->postal_address }}">
                            </div>
                            
                            <label for="phone_number" class="col-sm-2 control-label">Phone Number:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="phone_number" name="phone_number"  value="{{$labs->phone_number  }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email_address" class="col-sm-2 control-label">E-mail Address:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="email_address" name="email_address" value="{{ $labs->email_address }}">
                            </div>
                            
                            <label for="website" class="col-sm-2 control-label">Website:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="website" name="website" value="{{ $labs->website }}">
                            </div>
                        </div>
                        
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Days of Operation:</label>
                            <div class="col-sm-2">                               
                                <input type='checkbox' id='all_days' value='' >Select all
                            </div>
                            <div class="col-sm-2">                               
                                <input type='checkbox' id='d1' name='operational_days[]' value='Monday' {{ strpos($labs->operational_days, "Monday") !== false ? "checked":"" }}> Monday
                            </div>
                            <div class="col-sm-2">                               
                                <input type='checkbox' id='d2' name='operational_days[]' value='Tuesday' {{ strpos($labs->operational_days, "Tuesday") !== false ? "checked":"" }}> Tuesday
                            </div>
                            <div class="col-sm-2">                               
                                <input type='checkbox' id='d3' name='operational_days[]' value='Wednesday'{{ strpos($labs->operational_days, "Wednesday") !== false ? "checked":"" }}> Wednesday
                            </div> 
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-2">                               
                                <input type='checkbox' id='d4' name='operational_days[]' value='Thursday'{{ strpos($labs->operational_days, "Thursday") !== false ? "checked":"" }}>Thursday
                            </div> 
                            <div class="col-sm-2">                               
                                <input type='checkbox' id='d5' name='operational_days[]' value='Friday' {{ strpos($labs->operational_days, "Friday") !== false ? "checked":"" }}> Friday
                            </div> 
                            <div class="col-sm-2">                               
                                <input type='checkbox' id='d6' name='operational_days[]' value='Saturday'{{ strpos($labs->operational_days, "Saturday") !== false ? "checked":"" }} > Saturday
                            </div> 
                            <div class="col-sm-2">                               
                                <input type='checkbox' id='d7' name='operational_days[]' value='Sunday'{{ strpos($labs->operational_days, "Sunday") !== false ? "checked":"" }}> Sunday
                            </div> 
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Hours of Operation:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control"  id="operational_hours" name="operational_hours" value="{{ $labs->operational_hours }}" placeholder="24hrs / 08:00AM-06:00PM" >
                                @if ($errors->has('operational_hours'))
                                <span class="help-block">
                                    {{ $errors->first('operational_hours') }}
                                </span>                                 
                                @endif
                            </div>
                            
                        </div>
                        
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Ownership:<font color="red">*</font> </label></label>
                            <div class="col-sm-4">
                                <select class="form-control select2" id="ownership_id"  name="ownership_id" style="width: 100%;">
                                    <option value="">--Select Ownership--</option>
                                    @foreach(getOwnership() as $st)
                                    <option value="{{ $st->id }}" {{ ($labs->ownership_id == $st->id ? "selected":"") }}>{{ $st->name }}</option>
                                    
                                    @endforeach
                                </select>
                            </div>
                            <label class="col-sm-2 control-label">Ownership Type:</label>
                            <div class="col-sm-4">
                                <select class="form-control select2" id="ownership_type_id" name="ownership_type_id" style="width: 100%;">
                                    <option value="">--Choose one--</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="hs_ownership_details" class="col-sm-2 control-label">Ownership Details:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control"  id="ownership_details" name="ownership_details" value="">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Operation Status:<font color="red">*</font> </label></label>
                            <div class="col-sm-4">
                                <select class="form-control select2" id="operational_status_id" name="operational_status_id" style="width: 100%;">
                                    <option value="">--Select Operation Status--</option>
                                    @foreach(getLabOperationalStatus() as $st)
                                    <option value="{{ $st->id }}" {{ ($labs->operational_status_id == $st->id ? "selected":"") }}>{{ $st->status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="col-sm-2 control-label">Registration Status:</label>
                            <div class="col-sm-4">
                                <select class="form-control select2" id="registration_status_id" name="registration_status_id" style="width: 100%;">
                                    <option value="">--Select Registration Status--</option>
                                    @foreach(getlabRegistrationStatus() as $st)
                                    <option value="{{ $st->id }}" {{ ($labs->registration_status_id == $st->id ? "selected":"") }}>{{ $st->status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Accreditation Status:</label>
                            <div class="col-sm-4">
                                <select class="form-control select2" id="accreditation_status_id" name="accreditation_status_id" style="width: 100%;">
                                    <option value="">--Select Accreditation Status--</option>
                                    @foreach(getAccreditationStatus() as $st)
                                    <option value="{{ $st->id }}" {{ ($labs->accreditation_status_id == $st->id ? "selected":"") }}>{{ $st->status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="col-sm-2 control-label">License Status:</label>
                            <div class="col-sm-4">
                                <select class="form-control select2" id="license_status_id" name="license_status_id" style="width: 100%;">
                                    <option value="">--Select License Status--</option>
                                    @foreach(getLicenseStatus() as $st)
                                    <option value="{{ $st->id }}" {{ ($labs->license_status_id == $st->id ? "selected":"") }}>{{ $st->status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Laboratory Level:<font color="red">*</font> </label></label>
                            <div class="col-sm-4">
                                <select class="form-control select2" id="facility_level_id"  name="facility_level_id" style="width: 100%;">
                                    <option value="">--Select Level of Care--</option>
                                    @foreach(getLevelOfCare() as $st)
                                    <option value="{{ $st->id }}" {{ ($labs->facility_level_id == $st->id ? "selected":"") }}>{{ $st->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                        </div>
                        
                        
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Tab two --}}
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingThree">
                <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseThree">
                        Service Elements
                    </a>
                </h4>
            </div>
            <div id="collapseFour" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThree">
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Institution/ Stand Alone:<font color="red"></font> </label>
                        <div class="col-sm-8">
                            <select class="form-control select2" id="premises_type_id"  name="premises_type_id" style="width: 100%;">
                                <option value="">--Select Institution Type--</option>
                                @foreach(getPremisesType() as $st)
                                <option value="{{ $st->id }}" {{ $labs->premises_type_id == $st->id ? "selected":"" }}>{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        
                        <label class="col-sm-4 control-label">Laboratory Number:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control"  id="medical_laboratory_number" name="medical_laboratory_number" value="{{ $labs->medical_laboratory_number }}" placeholder="Public/Private Medical Laboratory Number">                    
                        </div>
                    </div>
                    {{--           
                        <div class="form-group" id="nat1">
                            <label class="col-sm-2 control-label">National Certification:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="certifications" name="certifications" style="width: 100%;">
                                    <option value="">--Choose one--</option>
                                    
                                </select>                 
                            </div>
                        </div>
                        <div class="form-group" id="nat2">
                            <label class="col-sm-2 control-label">Certification Date:</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="datepicker1" name="lb_dt_cert_ng">
                                </div>
                            </div>
                            <label class="col-sm-2 control-label">Expiration Date:</label>
                            <div class="col-sm-4">
                                <div class="input-group date" >
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="datepicker2" name="lb_dt_exp_cert_ng">
                                </div>
                            </div>
                        </div>
                        --}}
                        
                        <div class="form-group">
                            <label class="col-sm-4 control-label">External Quality Assurance Enrolment:</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="quality_assurance" name="quality_assurance" style="width: 100%;">
                                    <option value="">--Choose one--</option>
                                    <option value="1" {{ $labs->quality_assurance == 1 ? "selected":"" }}>Enrolled</option>
                                    <option value="2" {{ $labs->quality_assurance == 2 ? "selected":"" }}>Not Enrolled</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Specialized Laboratory Equipment:</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="equipments" name="equipments[]" style="width: 100%;">
                                    <option value="">--Choose one--</option>
                                    
                                </select>   
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Number of Laboratory Scientists:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control"  id="laboratory_scientists" name="laboratory_scientists" value="{{ $labs->laboratory_scientists }}">                    
                            </div>
                            
                        </div>
                        <div class="form-group">
                            
                            <label class="col-sm-4 control-label">Number of Laboratory Technicians:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control"  id="laboratory_technicians" name="laboratory_technicians" value="{{ $labs->laboratory_technicians }}">                    
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div><!-- end here-->
            
            
        </div>
        
        <!-- /.box-body -->
        <div class="box-footer">
            <a href="{{route('laboratory.index')}}">
                <button type="button" class="btn btn-warning">Return Back</button>
            </a>
            <button type="submit" class="btn btn-primary pull-right">Update Record</button>
        </div>
        <!-- /.box-footer -->
    </form>
    
    @endsection 
    
    @push('bk_script')
    @include('partials.dynamic_state_script')
    @include('partials.notification')

    <script src="{{asset("dist/iCheck/icheck.min.js")}}"></script>

    <script>
        
        $(document).ready(function () {
            
            $('#start_date').datepicker({
                autoclose: true            
            })

            $('input').iCheck({
                checkboxClass: 'icheckbox_minimal-green',
                increaseArea: '20%' // optional
            });

            $('#all_days').on('ifChecked', function(event){
                $('#d1, #d2, #d3, #d4, #d5,#d6, #d7').iCheck('check');
            });
            $('#all_days').on('ifUnchecked', function(event){
                $('#d1, #d2, #d3, #d4, #d5,#d6, #d7').iCheck('uncheck');
            });

            
        })
        
    </script>

    <script>
        //fill drop downs
        $("#state_id").val("{{$labs->state_id}}").change();
            $("#facility_level_id").val("{{$labs->facility_level_id}}").change();
            $("#ownership_id").val("{{$labs->ownership_id}}").change();
            $("#operational_status_id").val("{{$labs->operational_status_id}}").change();
            $("#registration_status_id").val("{{$labs->registration_status_id}}").change();
            $("#license_status_id").val("{{$labs->license_status_id}}").change();
            $("#accreditation_status_id").val("{{$labs->accreditation_status_id}}").change();
            
            //get and select lgas
            var stateID= {{$labs->state_id}};
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url:"{{route('getLgaList')}}",
                method:"POST",
                data:{id:stateID, _token:_token},
                success:function(result)
                {
                    $('#lga_id').html(result);
                    $("#lga_id").val({{$labs->lga_id}});
                }         
            })
            //get wards
            var lgaID = {{$labs->lga_id}};
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url:"{{route('getWardList')}}",
                method:"POST",
                data:{lgaId:lgaID,_token:_token},
                success:function(result)
                {
                    $('#ward_id').html(result);
                    $('#ward_id').val({{$labs->ward_id}});
                }         
            })
            
             
    </script>

    
    @endpush