<!-- Modal New -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Add LGA</h4>
        </div>
  
          <div class="modal-body">
              <div class="panel-body">
                <form action="{{route('lgas.store')}}" method="post">
                    @csrf()

                    <div class="form-group row {{ $errors->has('state_id') ? 'has-error' : '' }}">            
                        <label class="col-sm-4 control-label">State:<font color="red">*</font> </label>
                        <div class="col-sm-8">
                                <select class="form-control select2" id="state_id" name ="state_id" required  data-width="100%">
                                    <option value="">--Select State--</option>
                                    @foreach(getStates() as $st)
                                        <option value="{{$st->id}}">{{$st->name}}</option>
                                    @endforeach
                                </select>
                        
                        </div>
                    </div>

                      <div class="form-group row {{ $errors->has('name') ? 'has-error' : '' }}">
                          <label for="name" class="col-sm-4 control-label">LGA Name: <font color="red">*</font> </label>
                          <div class="col-sm-8">
                          <input type="text" class="form-control"  id="name"  name="name" required>
                              @if ($errors->has('name'))
                                  <span class="help-block">
                                      {{ $errors->first('name') }}
                                  </span>                                 
                              @endif
                          </div>
                      </div>

                      <div class="form-group row {{ $errors->has('lga_code') ? 'has-error' : '' }}">
                          <label  class="col-sm-4 control-label">LGA Code: <font color="red">*</font> </label>
                          <div class="col-sm-8">
                          <input type="text" class="form-control"  id="lga_code"  name="lga_code" placeholder="Two digit numeric code" required>
                              @if ($errors->has('lga_code'))
                                  <span class="help-block">
                                      {{ $errors->first('lga_code') }}
                                  </span>                                 
                              @endif
                          </div>
                      </div>
                    <div class="pull-right">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                              <button type="submit" id="save" class="btn btn-primary">Save</button>
                      </div>
                </form> 
            </div>
          </div>
    
        
      </div>
    </div>
</div>