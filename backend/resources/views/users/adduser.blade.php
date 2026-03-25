  <div class="modal fade" id="register" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">User Registration</h4>
              <div class='notifications top-right'></div>
            </div>
            <div class="modal-body">
              
                <div class="panel-body">
                    <form method="POST" action="{{ route('registeruser') }}" aria-label="{{ __('Register') }}">
                        @csrf
    
                        <div class="form-group row">
                            <label for="firstname" class="col-md-4 col-form-label text-md-right">{{ __('Fist Name') }}<font color="red">*</font></label>
    
                            <div class="col-md-8">
                                <input id="firstname" type="text" class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}" name="firstname" value="{{ old('firstname') }}" required autofocus>
    
                                <span class="text-danger">
                                    <strong id="firstname-error"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-group row">
                                <label for="lastname" class="col-md-4 col-form-label text-md-right">{{ __('Last Name') }} <font color="red">*</font></label>
    
                                <div class="col-md-8">
                                    <input id="lastname" type="text" class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}" name="lastname" value="{{ old('lastname') }}" required>
    
                                    <span class="text-danger">
                                        <strong id="lastname-error"></strong>
                                    </span>
                                </div>
                            </div>
    
                      
    
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }} <font color="red">*</font></label>
    
                            <div class="col-md-8">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>
    
                                <span class="text-danger">
                                     <strong id="email-error"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-group row">
                                <label for="mobile" class="col-md-4 col-form-label text-md-right">{{ __('Mobile Number') }}</label>
        
                                <div class="col-md-8">
                                    <input id="mobile" type="text" class="form-control{{ $errors->has('mobile') ? ' is-invalid' : '' }}" name="mobile" value="{{ old('mobile') }}" data-inputmask='"mask": "0999-999-9999"' data-mask>
        
                                    <span class="text-danger">
                                         <strong id="email-error"></strong>
                                    </span>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="job" class="col-md-4 col-form-label text-md-right">{{ __('Job Title') }}</label>
        
                                <div class="col-md-8">
                                    <input id="job" type="text" class="form-control" name="job" value="{{ old('job') }}">
        
                                    <span class="text-danger">
                                         <strong id="job-error"></strong>
                                    </span>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="organisation" class="col-md-4 col-form-label text-md-right">{{ __('Organisation') }}</label>
        
                                <div class="col-md-8">
                                    <input id="organisation" type="text" class="form-control" name="organisation" value="{{ old('organisation') }}">
        
                                    <span class="text-danger">
                                         <strong id="org-error"></strong>
                                    </span>
                                </div>
                        </div>
    
                        <div class="form-group row">
                                <label for="role" class="col-md-4 col-form-label text-md-right">{{ __('User Role') }} <font color="red">*</font></label>
                                <div class="col-md-8">
                                        <select class="form-control select2"  class="form-control" id="role" name="role[]"  data-placeholder="Select Role" required data-width="100%">
                                                @foreach(getRoles() as $role)
                                                    <option value="{{$role->id}}" {{ ($role->id == old('role') ? "selected":"") }}>{{$role->name}}</option>
                                                @endforeach
                                        </select>
                                <span class="text-danger">
                                    <strong id="role-error"></strong>
                                </span>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="state_id" class="col-md-4 col-form-label text-md-right">{{ __('State Permission') }} <font color="red">*</font></label>
                                <div class="col-md-8">
                                        <select class="form-control select2"  class="form-control" id="state_id" name="state_id" data-placeholder="Select State" required data-width="100%">
                                            <option value="">--Select State--</option>  
                                                @if (Auth::user()->state_id == 1 )
                                                    <option value="1">All States</option>    
                                                @endif
                                                @foreach(getAssignedState() as $s)
                                                    <option value="{{$s->id}}" {{ ($s->id == old('state_id') ? "selected":"") }}>{{$s->name}}</option>
                                                @endforeach
                                        </select>
                               
                                </div>
                        </div> 
                        <div class="form-group row">
                                <label for="state_id" class="col-md-4 col-form-label text-md-right">{{ __('LGA Permission') }} <font color="red">*</font></label>
                                <div class="col-md-8">
                                        <select class="form-control select2"  class="form-control" id="lga_id" name="lga_id[]"  data-placeholder="Select LGA" required data-width="100%">   
                                            
                                        </select>
                                
                                </div>
                        </div>          
                        <div class="pull-right">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" id="save" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
    
              
            </div>
        
          </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->