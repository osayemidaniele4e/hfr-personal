@extends("layouts.master")


@section('content-title')
Lookup Table
@if(auth()->user()->hasPermissionTo(71))
    <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addModal">
      Add Value
    </button>
@endif

@endsection

@section("content")
<div class="box">
    <div class="box-body">  
        <form class="form-horizontal"  action="{{route('dhis.lookupSearch')}}" method="get">
            @csrf

                <div class="form-group">
                        <div class="col-sm-5">  
                            <select class="form-control select2"  name ="type"  style="width: 100%;">
                                <option value="">--Select Type--</option>
                                <option value="Ownership" {{ ("Ownership" == old('type') ? "selected":"") }}>Ownership</option>
                                <option value="Level of Care"  {{ ("Level of Care" == old('type') ? "selected":"") }}>Level of Care</option>
                                <option value="Level of Care Option"  {{ ("Level of Care Option" == old('type') ? "selected":"") }}>Level of Care Option</option>
                                <option value="Ward"  {{ ("Ward" == old('type') ? "selected":"") }}>Ward</option>                             
                            </select>
                        </div>               
                  
                        <div class="col-sm-5" >
                            <input class="form-control input-sm"type="text" name="hfr_description" value = "{{old('hfr_description')}}" class="form-control" placeholder="description">
                        </div>
                      
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-success btn-block btn-sm">Search</button>
                        </div>
          
                  </div>
               
   

        </form>
      <hr>
  <div class="box-body">
    
    <table id="table1" class="table table-bordered table-striped" style="width:100%">
      <thead>
        <tr>
          <th>Type</th>
          <th>HFR Description</th>
          <th>HFR ID </th>
          <th>DHIS2 UID</th>
          @if(auth()->user()->hasPermissionTo(72))
          <th>Action</th>             
          @endif
        </tr>
      </thead>
      <tbody>
        
        @foreach($lookup as $l)
        <tr>
          <td>{{ $l->type }}</td>
          <td>{{$l->hfr_description}}</td>
          <td>{{$l->hfr_id}} </td>
          <td>{{$l->dhis_uid}}</td>
          @if(auth()->user()->hasPermissionTo(72))
            <td>
              <a href="#">
                <button class="btn btn-warning btn-sm"  type="button" data-toggle="modal" data-target="#edit"
                    data-id="{{$l->id}}" data-type="{{$l->type}}"  data-hfr_description="{{$l->hfr_description}}" 
                    data-dhis_uid="{{$l->dhis_uid}}"  data-hfr_id="{{$l->hfr_id}}"> Edit
                </button>
            </a> 
            </td>
          @endif

     
        </tr>
        @endforeach
      </tbody>

      <tfoot>
    
      </tfoot>
    </table>
    
    
  </div>
  <!-- /.box-body -->
  <div class="box-footer">
      <div class="row">
        
            @php
              $perpage = $lookup->perpage();
              $currentpage = $lookup->currentpage();
              $from = ($currentpage-1)*$perpage+1;
              
              if ($lookup->currentpage() == $lookup->lastpage()) {
                $to = $lookup->total();
              } else {
                $to = $currentpage*$perpage;
              }
            @endphp
       
            <div class="col-md-4">
                Showing {{$from}} to {{$to}} of {{$lookup->total()}} entries
               
            </div>
            <div class="col-md-8">
                <div class="pull-right">
                    {{$lookup->links()}}                  
                </div>
            </div>

      </div>
    </div>
</div>
<!-- /.box -->

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Add Lookup Value</h4>
        </div>
  
          <div class="modal-body">
              <div class="panel-body">
                <form action="{{route('dhis.lookupStore')}}" method="post">
                    @csrf()

                    <div class="form-group row ">            
                        <label class="col-sm-4 control-label">Value Type:<font color="red">*</font> </label>
                        <div class="col-sm-8">
                                <select class="form-control select2" id="type" name ="type" required  data-width="100%">
                                    <option value="">--Select Type--</option>
                                    <option value="Ownership">Ownership</option>
                                    <option value="Level of Care">Level of Care</option>
                                    <option value="Level of Care Option">Level of Care Option</option>
                                    <option value="Ward">Ward</option>  
                                </select>
                        
                        </div>
                    </div>
               
                      <div class="form-group row ">
                          <label for="name" class="col-sm-4 control-label">HFR Description: <font color="red">*</font> </label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control"  id="hfr_description"  name="hfr_description" required>
                          </div>
                      </div>
                      <div class="form-group row ">
                          <label for="name" class="col-sm-4 control-label">HFR ID: <font color="red">*</font> </label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control"  id="hfr_id"  name="hfr_id" required>
                          </div>
                      </div>
                      <div class="form-group row ">
                          <label for="name" class="col-sm-4 control-label">DHIS2 ID: <font color="red">*</font> </label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control"  id="dhis_uid"  name="dhis_uid" required>
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

<!-- Edit New -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Update Lookup Value</h4>
        </div>
  
          <div class="modal-body">
              <div class="panel-body">
                <form action="{{route('dhis.lookupUpdate')}}" method="post">
                    @csrf()
                    @method("PUT")
                    <input type="hidden" name="id" id="id2"> 

                    <div class="form-group row ">            
                        <label class="col-sm-4 control-label">Value Type:<font color="red">*</font> </label>
                        <div class="col-sm-8">
                                <select class="form-control select2" id="type2" name ="type" required  data-width="100%">
                                    <option value="">--Select Type--</option>
                                    <option value="Ownership">Ownership</option>
                                    <option value="Level of Care">Level of Care</option>
                                    <option value="Level of Care Option">Level of Care Option</option>
                                    <option value="Ward">Ward</option>  
                                </select>
                        
                        </div>
                    </div>
               
                      <div class="form-group row ">
                          <label for="name" class="col-sm-4 control-label">HFR Description: <font color="red">*</font> </label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control"  id="hfr_description2"  name="hfr_description" required>
                          </div>
                      </div>
                      <div class="form-group row ">
                          <label for="name" class="col-sm-4 control-label">HFR ID: <font color="red">*</font> </label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control"  id="hfr_id2"  name="hfr_id" required>
                          </div>
                      </div>
                      <div class="form-group row ">
                          <label for="name" class="col-sm-4 control-label">DHIS2 ID: <font color="red">*</font> </label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control"  id="dhis_uid2"  name="dhis_uid" required>
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


@endsection 


@push("bk_script")
@include('partials.notification')

<script>
  $(document).ready( function () {


      $('#edit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var modal = $(this)
            
            modal.find('.modal-body #id2').val(button.data('id'))
            modal.find('.modal-body #type2').val(button.data('type')).change();
            modal.find('.modal-body #hfr_description2').val(button.data('hfr_description'));
            modal.find('.modal-body #hfr_id2').val(button.data('hfr_id'));
            modal.find('.modal-body #dhis_uid2').val(button.data('dhis_uid'));

         
       
        });//end




  } );
</script>
@endpush