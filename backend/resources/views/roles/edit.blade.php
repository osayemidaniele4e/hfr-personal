@extends("layouts.master")

@section("bk_css")
@endsection 

@section('content-title')

@endsection

@section("content")


<form class="form-horizontal" action="{{route('roles.update')}}" method="POST">
        @csrf
        
        <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                        <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Edit User Role
                                </a>
                        </h4>
                </div>
                
                <div class="panel-body">
                        <div class="box-body">
                                <input type="hidden" name="id" value="{{$role->id}}">
                                <div class="form-group row">
                                        <label style="text-align: right;" class="col-md-2 col-form-label text-md-right">Role name:<font color="red">*</font> </label>
                                        <div class="col-md-8">
                                                <input id="role" type="text" class="form-control" name="name" value="{{$role->name}}" required autofocus>
                                                <span class="text-danger">
                                                        <strong id="role-error"></strong>
                                                </span>
                                        </div>
                                </div>      
                                <div class="form-group row">
                                        <label style="text-align: right;" class="col-md-2 col-form-label text-md-right">Description:<font color="red">*</font> </label>
                                        <div class="col-md-8">
                                                <input id="description" type="text" class="form-control" value="{{$role->description}}" name="description" required>
                                                <span class="text-danger">
                                                        <strong id="descr-error"></strong>
                                                </span>
                                        </div>
                                </div> 
                                <div class="form-group row">
                                                <label style="text-align: right;" class="col-md-2 col-form-label text-md-right">Subordinate Roles: </label>
                                                <div class="col-md-8">
                                                     <select class="form-control select2"  class="form-control" id="roles_below" name="roles_below[]" multiple="multiple"  data-placeholder="Select Roles" data-width="100%">
                                                                @foreach(getRolesAll() as $rol)
                                                                        <option value="{{ $rol->id }}" {{ in_array($rol->id, explode(',', $role->roles_below)) ? "selected":"" }}>{{$rol->name}}</option>
                                                                @endforeach
                                                        </select>
                                                <span class="text-danger">
                                                        <strong id="role_below"></strong>
                                                </span>
                                                </div>
                                </div> 
                                <div class="form-group row">
                                        <label class="col-sm-2" style="text-align: right;">Permissions:<font color="red">*</font></label>
                                        <div class="col-sm-2">                               
                                                <input type='checkbox' id='check_all'> Check All                                                  
                                        </div>
                                        
                                </div>
                                
                                <div class="form-group row">
                                                <label class="col-sm-2"></label>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='62'> View Masters                                            
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                    <label class="col-sm-2"></label>
                                                    <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='47'> View States                                                    
                                                    </div>
                                                    <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='48'> Add States                                                   
                                                    </div>
                                                    <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='49'> Update States                                                
                                                    </div>
                                                    <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='50'> Delete States                                                       
                                                    </div>
                                                    <div class="col-sm-2"></div>                              
                                           </div>
                                           <div class="form-group row">
                                                <label class="col-sm-2"></label>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='51'> View LGA                                                    
                                                </div>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='52'> Add LGA                                                   
                                                </div>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='53'> Update LGA                                              
                                                </div>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='54'> Delete LGA                                                       
                                                </div>
                                                <div class="col-sm-2"></div>                              
                                        </div>
                                        <div class="form-group row">
                                                <label class="col-sm-2"></label>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='55'> View Wards                                                    
                                                </div>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='56'> Add Wards                                                   
                                                </div>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='57'> Update Wards                                               
                                                </div>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='58'> Delete Wards                                                       
                                                </div>
                                                <div class="col-sm-2"></div>                              
                                        </div>
                                        <div class="form-group row">
                                                <label class="col-sm-2"></label>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='31'> View Hospital Services                                                    
                                                </div>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='32'> Add Hospital Services                                                 
                                                </div>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='33'> Update Hospital Services                                               
                                                </div>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='34'> Delete Hospital Services                                                      
                                                </div>
                                                <div class="col-sm-2"></div>                              
                                        </div>
                                        <div class="form-group row">
                                                        <label class="col-sm-2"></label>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='35'> View Imaging Services                                                    
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='36'> Add Imaging Services                                                 
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='37'> Update Imaging Services                                               
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='38'> Delete Imaging Services                                                      
                                                        </div>
                                                        <div class="col-sm-2"></div>                              
                                                </div>
                                                <div class="form-group row">
                                                        <label class="col-sm-2"></label>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='39'> View Lab Equipments                                                    
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='40'> Add Lab Equipments                                                 
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='41'> Update Lab Equipments                                               
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='42'> Delete Lab Equipments                                                      
                                                        </div>
                                                        <div class="col-sm-2"></div>                              
                                                </div>
                                                <div class="form-group row">
                                                        <label class="col-sm-2"></label>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='43'> View Lab Certification                                                    
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='44'> Add Lab Certification                                                
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='45'> Update Lab Certification                                              
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='46'> Delete Lab Certification                                                     
                                                        </div>
                                                        <div class="col-sm-2"></div>      
                                                </div>
                                                <hr>                        
                
                                           <div class="form-group row">
                                                        <label class="col-sm-2"></label>
                                                        <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='1'> View Hospitals                                                    
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='2'> Add Hospitals                                                   
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='3'> Update Hospitals                                                
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='4'> Delete Hospitals                                                       
                                                        </div>
                                                        <div class="col-sm-2"></div>                              
                                               </div>
                                               <div class="form-group row">
                                                        <label class="col-sm-2"></label>
                                                        <div class="col-sm-4">                               
                                                                <input type='checkbox'name='permissions[]' value='67'> Adminstrator Facility Update                                                                                                                                                      
                                                        </div>
                                               </div>
                                            <div class="form-group row">
                                                <div class="col-sm-2"></div>   
                                                <div class="col-sm-2">                               
                                                    <input type='checkbox'name='permissions[]' value='5'> View Laboratories 
                                                </div>
                                                <div class="col-sm-2">                               
                                                    <input type='checkbox'name='permissions[]' value='6'> Add Laboratories
                                                </div>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='7'> Update Laboratories  
                                                </div>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='8'> Delete Laboratories
                                                </div>
                                                <div class="col-sm-2"></div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-2"></div>   
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='9'> View Pharmacies
                                                </div>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='10'> Add Pharmacies
                                                </div>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='11'> Update Pharmacies  
                                                </div>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='12'> Delete Pharmacies  
                                                </div>
                                                <div class="col-sm-2"></div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-2"></div>   
                                                <div class="col-sm-2">                               
                                                    <input type='checkbox'name='permissions[]' value='13'> View Radiologies
                                                </div>
                                                <div class="col-sm-2">                               
                                                    <input type='checkbox'name='permissions[]' value='14'> Add Radiologies
                                                </div>
                                                <div class="col-sm-2">                               
                                                    <input type='checkbox'name='permissions[]' value='15'> Update Radiologies
                                                </div>
                                                <div class="col-sm-2">                               
                                                        <input type='checkbox'name='permissions[]' value='16'> Delete Radiologies
                                                </div>
                                                <div class="col-sm-2"></div>
                                            </div>
                                            <hr>
                                            <div class="form-group row">
                                                    <div class="col-sm-2"></div>   
                                                    <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='17'> View Users
                                                    </div>
                                                    <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='18'> Add Users
                                                    </div>
                                                    <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='19'> Update Users
                                                    </div>
                                                    <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='20'> Delete Users
                                                    </div>
                                                    <div class="col-sm-2"></div>
                                            </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-2"></div>   
                                                    <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='21'> View Roles
                                                    </div>
                                                    <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='22'> Add Roles
                                                    </div>
                                                    <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='23'> Update Roles
                                                    </div>
                                                    <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='24'> Delete Roles
                                                    </div>
                                                    <div class="col-sm-2"></div>
                                            </div>
                  <hr>
                                            <div class="form-group row">
                                                    <div class="col-sm-2"></div>   
                                                    <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='25'> View Resources
                                                    </div>
                                                    <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='26'> Add Resources
                                                    </div>
                                                    <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='27'> Update Resources
                                                    </div>
                                                    <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='28'> Delete Resources
                                                    </div>
                                                    <div class="col-sm-2"></div>
                                            </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-2"></div>   
                                                    <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='29'> View Feedbacks
                                                    </div>
                                                    <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='30'> View Download Request
                                                    </div>
                                                    <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='63'> Receive User's Feedback                                                                                                                                                
                                                    </div>
                                                  
                                                    <div class="col-sm-2"></div>
                                            </div>
                                            <hr>
                                            <div class="form-group row">
                                                        <label class="col-sm-2"></label>
                                                      
                                                        <div class="col-sm-2">                               
                                                                 <input type='checkbox'name='permissions[]' value='59'> Verify Facility
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='60'> Validate Facility                                                   
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                            <input type='checkbox'name='permissions[]' value='61'> Publish Facility                                               
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='68'> View Approval Tracking                                                   
                                                        </div>
                                                        <div class="col-sm-2"></div>                              
                                               </div>
                                               <div>
                
                                               </div>
                                               <hr>                      
                                               <div class="form-group row">
                                                        <label class="col-sm-2"></label>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='66'> View DHIS2 Logs
                                                        </div>
                  
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='70'> View Lookup Values
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='71'> Add Lookup Values
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='72'> Edit Lookup Values
                                                        </div>
                                                
                                                        <div class="col-sm-2"></div>                              
                                               </div>
                                               <div class="form-group row">
                                                        <label class="col-sm-2"></label>
                                                        <div class="col-sm-4">  
                                                                        <input type='checkbox'name='permissions[]' value='64'> Receive DHIS2 Notifications                             
                                                        </div>
                                                        
                                                     
                                              
                                               </div>
                                               <hr>
                                               <div class="form-group row">
                                                        <label class="col-sm-2"></label>
                                                        <div class="col-sm-2">                               
                                                                <input type='checkbox'name='permissions[]' value='65'> View Reports
                                                        </div>
                                               </div>
                                               <div class="form-group row">
                                                        <label class="col-sm-2"></label>
                
                                                        <div class="col-sm-2">                               
                                                                        <input type='checkbox'name='permissions[]' value='73'> Facilities Update Report
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                                        <input type='checkbox'name='permissions[]' value='74'> Facilities Status Summary Report
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                                        <input type='checkbox'name='permissions[]' value='75'> Facilities Status Details Report
                                                        </div>
                                                        <div class="col-sm-2">                               
                                                                        <input type='checkbox'name='permissions[]' value='76'> Services Rendered Report
                                                        </div>
                                                        
                                               </div>
                                               <div class="form-group row">
                                                        <label class="col-sm-2"></label>
                
                                                        <div class="col-sm-2">                               
                                                                        <input type='checkbox'name='permissions[]' value='69'> Approvers Summary Report
                                                        </div>
                                               </div>
                  
                      
                                
                        </div> <!-- /.box-body inner --> 
                </div>   <!-- /.box-body -->                
        </div>
        <!-- /. panel -->
        <div class="box-footer">
                <a href="{{route('roles.index')}}">
                        <button type="button" class="btn btn-warning">Return Back</button>
                </a>
                <button type="submit" class="btn btn-primary pull-right">Update</button>
        </div>
        <!-- /.box-footer -->
</form>

@endsection 



@push('bk_script')
@include('partials.notification')

<script type="text/javascript">
        $(document).ready(function() {
             

                $('#check_all').click(function() {
                        var c = this.checked;
                        $(':checkbox').prop('checked',c);
                });
        });
        
        var permissions = @json($permission);
      
        
        $('input[type=checkbox]').each(function () {
                var id = $(this).val();
                if (ValueExist(id,permissions)==1) {
                        $(this).attr('checked', true);
                }
        });

        function ValueExist(value,arr){
                var status = '0';
                
                for(var i=0; i<arr.length; i++){
                        var name = arr[i];
                        if(name == value){
                                status = '1';
                                break;
                        }
                }
                return status;
        }
</script>


@endpush