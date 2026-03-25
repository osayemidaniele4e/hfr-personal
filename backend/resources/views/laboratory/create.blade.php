@extends("layouts.master")


@section("bk_css")
    <link rel="stylesheet" href="{{asset("dist/iCheck/minimal/green.css")}}"/>
@endsection 

@section("content")


<form class="form-horizontal" action="{{route('laboratory.store')}}" method="POST">
    @csrf
    
    
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
                                <input type="text" class="form-control"  id="registration_no" name="registration_no" value="" placeholder="Corporate Affairs Registration Number">
                            </div>
                            
                            <label class="col-sm-2 control-label">Commencement Date:</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="start_date" name="start_date" autocomplete="off">
                                    
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="reg_fac_name" class="col-sm-2 control-label">Registered Name: <font color="red">*</font> </label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="facility_name"  name="facility_name" value="" placeholder="Registered Facility Name">
                            </div>
                            
                            <label for="alt_facility_name" class="col-sm-2 control-label">Alternate Name:</label> 
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="alt_facility_name" name="alt_facility_name" value="" placeholder="Alternate Facility Name">
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
                                <input type="text" class="form-control"  id="house_no" name="house_no" value="">
                            </div>
                            
                            <label for="street_name" class="col-sm-2 control-label">Street Name:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="street_name"  name="street_name" value="">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="latitude" class="col-sm-2 control-label">Latitude:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="latitude" name="latitude"  value="">
                            </div>
                            
                            <label for="longitude" class="col-sm-2 control-label">Longitude:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="longitude" name="longitude" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="postal_address" class="col-sm-2 control-label">Postal Address:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="postal_address"  name="postal_address" value="">
                            </div>
                            
                            <label for="phone_number" class="col-sm-2 control-label">Phone Number:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="phone_number" name="phone_number"  value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email_address" class="col-sm-2 control-label">E-mail Address:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="email_address" name="email_address" value="">
                            </div>
                            
                            <label for="website" class="col-sm-2 control-label">Website:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"  id="website" name="website" value="">
                            </div>
                        </div>
                        
                        
                    <div class="form-group">
                            <label class="col-sm-2 control-label">Days of Operation:</label>
                            <div class="col-sm-2">                               
                                    <input type='checkbox' id='all_days' value='' >Select all
                            </div>
                            <div class="col-sm-2">                               
                                    <input type='checkbox' id='d1' name='operational_days[]' value='Monday' {{ (is_array(old('operational_days')) && in_array("Monday", old('operational_days'))) ? "checked":"" }}> Monday
                            </div>
                            <div class="col-sm-2">                               
                                    <input type='checkbox' id='d2' name='operational_days[]' value='Tuesday' {{ (is_array(old('operational_days')) && in_array("Tuesday", old('operational_days'))) ? "checked":"" }}> Tuesday
                            </div>
                            <div class="col-sm-2">                               
                                    <input type='checkbox' id='d3' name='operational_days[]' value='Wednesday'{{ (is_array(old('operational_days')) && in_array("Wednesday", old('operational_days'))) ? "checked":"" }}> Wednesday
                            </div> 
                    </div>

                    <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-2">                               
                                    <input type='checkbox' id='d4'  name='operational_days[]' value='Thursday'{{ (is_array(old('operational_days')) && in_array("Thursday", old('operational_days'))) ? "checked":"" }}>Thursday
                            </div> 
                            <div class="col-sm-2">                               
                                    <input type='checkbox' id='d5' name='operational_days[]' value='Friday' {{ (is_array(old('operational_days')) && in_array("Friday", old('operational_days'))) ? "checked":"" }}> Friday
                            </div> 
                            <div class="col-sm-2">                               
                                    <input type='checkbox'id='d6'  name='operational_days[]' value='Saturday'{{ (is_array(old('operational_days')) && in_array("Saturday", old('operational_days'))) ? "checked":"" }} > Saturday
                            </div> 
                            <div class="col-sm-2">                               
                                    <input type='checkbox' id='d7' name='operational_days[]' value='Sunday'{{ (is_array(old('operational_days')) && in_array("Sunday", old('operational_days'))) ? "checked":"" }}> Sunday
                            </div> 
                    </div>
                   
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Hours of Operation:</label>
                        <div class="col-sm-10">
                                <input type="text" class="form-control"  id="operational_hours" name="operational_hours" value="{{ old('operational_hours') }}" placeholder="24hrs / 08:00AM-06:00PM" >
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
                                            <option value="{{ $st->id }}" {{ (old('ownership_id') == $st->id ? "selected":"") }}>{{ $st->name }}</option>
                                            
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
                                    <option value="{{ $st->id }}" {{ (old('operational_status_id') == $st->id ? "selected":"") }}>{{ $st->status }}</option>
                                    @endforeach
                                </select>
                        </div>
                        <label class="col-sm-2 control-label">Registration Status:</label>
                        <div class="col-sm-4">
                                <select class="form-control select2" id="registration_status_id" name="registration_status_id" style="width: 100%;">
                                    <option value="">--Select Registration Status--</option>
                                    @foreach(getlabRegistrationStatus() as $st)
                                    <option value="{{ $st->id }}" {{ (old('registration_status_id') == $st->id ? "selected":"") }}>{{ $st->status }}</option>
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
                                    <option value="{{ $st->id }}" {{ (old('accreditation_status_id') == $st->id ? "selected":"") }}>{{ $st->status }}</option>
                                    @endforeach
                                </select>
                        </div>
                        <label class="col-sm-2 control-label">License Status:</label>
                        <div class="col-sm-4">
                                <select class="form-control select2" id="license_status_id" name="license_status_id" style="width: 100%;">
                                        <option value="">--Select License Status--</option>
                                        @foreach(getLicenseStatus() as $st)
                                        <option value="{{ $st->id }}" {{ (old('license_status_id') == $st->id ? "selected":"") }}>{{ $st->status }}</option>
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
                                        <option value="{{ $st->id }}" {{ (old('facility_level_id') == $st->id ? "selected":"") }}>{{ $st->name }}</option>
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
                                    <option value="{{ $st->id }}" {{ (old('premises_type_id') == $st->id ? "selected":"") }}>{{ $st->name }}</option>
                                    @endforeach
                                </select>
                        </div>
                </div>
                <div class="form-group">
                
                    <label class="col-sm-4 control-label">Laboratory Number:</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control"  id="medical_laboratory_number" name="medical_laboratory_number" value="" placeholder="Public/Private Medical Laboratory Number">                    
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
                            <option value="1">Enrolled</option>
                            <option value="2">Not Enrolled</option>
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
                        <input type="text" class="form-control"  id="laboratory_scientists" name="laboratory_scientists">                    
                    </div>
            
                </div>
                <div class="form-group">
         
                    <label class="col-sm-4 control-label">Number of Laboratory Technicians:</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control"  id="laboratory_technicians" name="laboratory_technicians">                    
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
    <button type="submit" class="btn btn-primary pull-right">Submit Record</button>
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


            
        //get ownership  type ownership_type_id
        $("#ownership_id").change(function(){
            if($(this).val() != "") //if specialized 
            {    
                var id= $('#ownership_id').val();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{route('getOwnershipType')}}",
                    method:"POST",
                    data:{ownership_id:id,_token:_token},
                    success:function(result)
                    {
                        $('#ownership_type_id').html(result);
                    }         
                })            
            }
        });
        
    })
    
</script>

@endpush