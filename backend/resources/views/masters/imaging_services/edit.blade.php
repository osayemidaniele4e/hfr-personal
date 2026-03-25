<!-- Modal New -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Update Imaging Service</h4>
        </div>
  
          <div class="modal-body">
              <div class="panel-body">
              <form action="{{route('imaging-services.update','id')}}" method="post">
                  @csrf
                  @method("PUT")
                  <input type="hidden" name="id" id="id">
                  
                      <div class="form-group row {{ $errors->has('service_name1') ? 'has-error' : '' }}">
                          <label class="col-sm-4 control-label">Service name: <font color="red">*</font> </label>
                          <div class="col-sm-8">
                          <input type="text" class="form-control"  id="service_name1"  name="service_name1" value="{{old('service_name1')}}" required>
                              @if ($errors->has('service_name1'))
                                  <span class="help-block">
                                      {{ $errors->first('service_name1') }}
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