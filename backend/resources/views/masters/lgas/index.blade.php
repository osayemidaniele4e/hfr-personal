@extends("layouts.master")


@section('content-title')
Local Government Areas (LGAs)	

@if(auth()->user()->hasPermissionTo(52))
    <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addModal">
      Add LGA
    </button>
@endif

@endsection

@section("content")
<div class="box">
        <div class="box-body">  
            <form class="form-horizontal"  action="{{route('lga.search')}}" method="get">
                @csrf
    
                    <div class="form-group">
                            <div class="col-sm-4">  
                                <select class="form-control select2" id="state" name ="state"  style="width: 100%;">
                                    <option value="1">--Select State--</option>
                                    @foreach(getStates() as $st)
                                        <option value="{{$st->id}}">{{$st->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        
                          
                      
                            <div class="col-sm-4" >
                                <input class="form-control input-sm"type="text" name="name" id="name" class="form-control" placeholder="lga name">
                            </div>
                            <div class="col-sm-2">
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-success btn-block btn-sm">Search</button>
                            </div>
              
                      </div>
                   
       
    
            </form>
                <hr>


          <table id="table1" class="table table-bordered table-striped" style="width:100%">
            <thead>
              <tr>
                <th>State</th>
                <th>LGA Name</th>
                <th>LGA Code</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
           
              @foreach($lgas as $lga)
              <tr>
                <td>{{$lga->state->name}}</td>
                <td>{{$lga->name}}</td>
                <td>{{$lga->lga_code}}</td>
                <td>
                    @if(auth()->user()->hasPermissionTo(53))
                      <a href="#">
                        <button class="btn btn-warning btn-sm" data-id="{{$lga->id}}" data-name="{{$lga->name}}"  data-lga_code="{{$lga->lga_code}}" 
                            data-state_id="{{$lga->state_id}}"  type="button" data-toggle="modal" data-target="#editModal">Edit</button>
                      </a>
                    @endif
                    @if(auth()->user()->hasPermissionTo(54))
                      <a href="#">
                        <button class="btn btn-danger btn-sm" data-id="{{$lga->id}}" data-name="{{$lga->name}}" type="button" data-toggle="modal" data-target="#deleteModal" > Delete</button>
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
                    $perpage = $lgas->perpage();
                    $currentpage = $lgas->currentpage();
                    $from = ($currentpage-1)*$perpage+1;
                    
                    if ($lgas->currentpage() == $lgas->lastpage()) {
                      $to = $lgas->total();
                    } else {
                      $to = $currentpage*$perpage;
                    }
                  @endphp
             
                  <div class="col-md-4">
                      Showing {{$from}} to {{$to}} of {{$lgas->total()}} entries
                     
                  </div>
                  <div class="col-md-8">
                      <div class="pull-right">
                          {{$lgas->links()}}                  
                      </div>
                  </div>

            </div>
          </div>
      </div>
      <!-- /.box -->
@endsection 

@include('masters.lgas.create')
@include('masters.lgas.edit')
@include('masters.lgas.delete')



@push("bk_script")
@include('partials.notification')

<script>
  $(document).ready( function () {
  

  });

  $('#editModal').on('show.bs.modal', function (event) {
      $('#name1').focus();

      var button = $(event.relatedTarget)

      var modal = $(this)
      
      modal.find('.modal-body #id1').val(button.data('id'))
      modal.find('.modal-body #name1').val(button.data('name'))
      modal.find('.modal-body #lga_code1').val(button.data('lga_code'))
      $("#state_id1").val(button.data('state_id')).change();

    });
    
    $('#deleteModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget) 
      var id = button.data('id')
      var message =  "Are you sure you want to delete '".concat(button.data('name'), "' LGA?") ;
      var modal = $(this)
      modal.find('.modal-body #message').text(message);
      modal.find('.modal-body #lga_id').val(id)
    })


</script>
@endpush