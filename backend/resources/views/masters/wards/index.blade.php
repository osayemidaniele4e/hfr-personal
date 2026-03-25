@extends("layouts.master")


@section('content-title')
Wards	
@if(auth()->user()->hasPermissionTo(56))
    <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addModal">
      Add Ward
    </button>
@endif

@endsection

@section("content")
<div class="box">
        <div class="box-body">  
            <form class="form-horizontal"  action="{{route('wards.search')}}" method="get">
                @csrf
    
                    <div class="form-group">
                            <div class="col-sm-3">  
                                <select class="form-control select2" id="state" name ="state"  style="width: 100%;">
                                    <option value="1">--Select State--</option>
                                    @foreach(getStates() as $st)
                                        <option value="{{$st->id}}">{{$st->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        
                            <div class="col-sm-3">
                                <select class="form-control select2" id="lga" name="lga"  style="width: 100%;">
                                    <option value="1">--Select LGA--</option>
                                </select>
                            </div>
                      
                            <div class="col-sm-4" >
                                <input class="form-control input-sm"type="text" name="ward_name" id="ward_name" class="form-control" placeholder="Ward name">
                            </div>
                  
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-success btn-block btn-sm">Search</button>
                            </div>
              
                      </div>
                   
       
    
            </form>
                <hr>


          
          <table id="table1" class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th>State</th>
                <th>LGA</th>
                <th>Ward ID</th>
                <th>Ward</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
           
                @foreach($wards as $w)
                <tr>
                  <td>{{$w->state}}</td>
                  <td>{{$w->lga}}</td>
                  <td>{{$w->id}}</td>
                  <td>{{$w->name}}</td>
                  <td>
                      @if(auth()->user()->hasPermissionTo(57))
                        <a href="#">
                          <button class="btn btn-warning btn-sm" data-id="{{$w->id}}" data-name="{{$w->name}}"  data-state_id="{{$w->state_id}}" 
                              data-lga_id="{{$w->lga_id}}"  type="button" data-toggle="modal" data-target="#editModal">Edit</button>
                        </a>
                      @endif
                      @if(auth()->user()->hasPermissionTo(58))
                        <a href="#">
                          <button class="btn btn-danger btn-sm" data-id="{{$w->id}}" data-name="{{$w->name}}"  type="button" data-toggle="modal" data-target="#deleteModal" > Delete</button>
                        </a>
                      @endif
                  </td>
                </tr>
                @endforeach
                
              </tbody>
          
          </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <div class="row">
              
                  @php
                    $perpage = $wards->perpage();
                    $currentpage = $wards->currentpage();
                    $from = ($currentpage-1)*$perpage+1;
                    
                    if ($wards->currentpage() == $wards->lastpage()) {
                      $to = $wards->total();
                    } else {
                      $to = $currentpage*$perpage;
                    }
                  @endphp
             
                  <div class="col-md-4">
                      Showing {{$from}} to {{$to}} of {{$wards->total()}} entries
                     
                  </div>
                  <div class="col-md-8">
                      <div class="pull-right">
                          {{$wards->links()}}                  
                      </div>
                  </div>

            </div>
          </div>
</div>
      <!-- /.box -->
@endsection 

@include('masters.wards.create')
@include('masters.wards.edit')
@include('masters.wards.delete')


@push("bk_script")

@include('partials.dynamic_lgas_only')
@include('partials.notification')


<script>
  $(document).ready( function () {


  });
  
  $('#editModal').on('show.bs.modal', function (event) {
      $('#name1').focus();

      var button = $(event.relatedTarget)

      var modal = $(this)
      
      modal.find('.modal-body #name1').val(button.data('name'));
      modal.find('.modal-body #id1').val(button.data('id'));
      $("#state_id1").val(button.data('state_id')).change();

       //get lgas
      var stateID= button.data('state_id');
      var _token = $('input[name="_token"]').val();
      $.ajax({
          url:"{{route('getLgaList')}}",
          method:"POST",
          data:{id:stateID, _token:_token},
          success:function(result)
          {
              $('#lga_id1').html(result);
              $("#lga_id1").val(button.data('lga_id'));
          }         
      })

  });
    
  $('#deleteModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget) 
      
      var id = button.data('id')
      var message =  "Are you sure you want to delete '".concat(button.data('name'), "' ward?") ;
      var modal = $(this)
      modal.find('.modal-body #message').text(message);
      modal.find('.modal-body #ward_id').val(id)
  })

     //if state change for edit form fill lga
    $('#state_id1').change(function(){
          if($(this).val() != '')
          {
              var stateID= $('#state_id1').val();
              var _token = $('input[name="_token"]').val();
              $.ajax({
                  url:"{{route('getLgaList')}}",
                  method:"POST",
                  data:{id:stateID, _token:_token},
                  success:function(result)
                  {
                      $('#lga_id1').html(result);
                  }         
              })
          }
      });


    //populate lga after state change for search fields
    $('#state').change(function(){
          if($(this).val() != '')
          {
              var stateID= $('#state').val();
              var _token = $('input[name="_token"]').val();
              $.ajax({
                  url:"{{route('getLgaList')}}",
                  method:"POST",
                  data:{id:stateID, _token:_token},
                  success:function(result)
                  {
                      $('#lga').html(result);
                  }         
              })
          }
      });


</script>

@endpush