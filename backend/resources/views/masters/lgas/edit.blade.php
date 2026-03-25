<!-- Modal New -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Update LGA</h4>
        </div>
  
          <div class="modal-body">
              <div class="panel-body">
                <form action="{{route('lgas.update','id')}}" method="post">
                    @csrf()
                    @method("PUT")

                    <input type="hidden" name="id1" id="id1">

                    <div class="form-group row {{ $errors->has('state_id1') ? 'has-error' : '' }}">            
                        <label class="col-sm-4 control-label">State:<font color="red">*</font> </label>
                        <div class="col-sm-8">
                                <select class="form-control select2" id="state_id1" name ="state_id1" required  data-width="100%">
                                    <option value="">--Select State--</option>
                                    @foreach(getStates() as $st)
                                        <option value="{{$st->id}}">{{$st->name}}</option>
                                    @endforeach
                                </select>
                        
                        </div>
                    </div>

                      <div class="form-group row {{ $errors->has('name1') ? 'has-error' : '' }}">
                          <label for="name" class="col-sm-4 control-label">LGA Name: <font color="red">*</font> </label>
                          <div class="col-sm-8">
                          <input type="text" class="form-control"  id="name1"  name="name1" required>
                              @if ($errors->has('name1'))
                                  <span class="help-block">
                                      {{ $errors->first('name1') }}
                                  </span>                                 
                              @endif
                          </div>
                      </div>

                      <div class="form-group row {{ $errors->has('lga_code1') ? 'has-error' : '' }}">
                          <label  class="col-sm-4 control-label">LGA Code: <font color="red">*</font> </label>
                          <div class="col-sm-8">
                          <input type="text" class="form-control"  id="lga_code1"  name="lga_code1" required>
                              @if ($errors->has('lga_code'))
                                  <span class="help-block">
                                      {{ $errors->first('lga_code1') }}
                                  </span>                                 
                              @endif
                          </div>
                      </div>
                    <div class="pull-right">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                              <button type="submit" id="save" class="btn btn-primary">Update</button>
                      </div>
                </form> 
            </div>
          </div>
    
        
      </div>
    </div>
</div>