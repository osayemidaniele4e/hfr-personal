<!-- Modal New -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Update State</h4>
        </div>
  
          <div class="modal-body">
              <div class="panel-body">
              <form action="{{route('states.update','id')}}" method="post">
                  @csrf
                  @method("PUT")
                  <input type="hidden" name="id" id="id">
                  
                  <div class="form-group row {{ $errors->has('name') ? 'has-error' : '' }}">
                      <label for="name" class="col-sm-4 control-label">State Name: <font color="red">*</font> </label>
                      <div class="col-sm-8">
                      <input type="text" class="form-control"  id="name1"  name="name1" required>
                          @if ($errors->has('name'))
                              <span class="help-block">
                                  {{ $errors->first('name') }}
                              </span>                                 
                          @endif
                      </div>
                  </div>
                  <div class="form-group row {{ $errors->has('short_code') ? 'has-error' : '' }}">
                      <label  class="col-sm-4 control-label">Short Code: <font color="red">*</font> </label>
                      <div class="col-sm-8">
                      <input type="text" class="form-control"  id="short_code1"  name="short_code1" required>
                          @if ($errors->has('short_code'))
                              <span class="help-block">
                                  {{ $errors->first('short_code') }}
                              </span>                                 
                          @endif
                      </div>
                  </div>
                  <div class="form-group row {{ $errors->has('num_code1') ? 'has-error' : '' }}">
                        <label  class="col-sm-4 control-label">Numeric Code: <font color="red">*</font> </label>
                        <div class="col-sm-8">
                        <input type="text" class="form-control"  id="num_code1"  name="num_code1" required>
                            @if ($errors->has('num_code1'))
                                <span class="help-block">
                                    {{ $errors->first('num_code1') }}
                                </span>                                 
                            @endif
                        </div>
                    </div>
                    <div class="pull-right">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary">Update</button>
                      </div>
                </form> 
            </div>
          </div>
    
        
      </div>
    </div>
</div>