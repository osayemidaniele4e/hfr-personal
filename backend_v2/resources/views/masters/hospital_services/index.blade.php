@extends("layouts.master")

@section('content-title')
Hospital Services

@if(auth()->user()->hasPermissionTo(32))
    <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addModal">
      Add Service
    </button>
@endif


@endsection

@section("content")
<div class="box">
  <div class="box-body">
    <table id="table1" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Service Category</th>
          <th>Service</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        
        @foreach($services as $service)
        <tr>
          <td>{{$service->ServiceCategory->description}}</td>
          <td>{{$service->name}}</td>
          <td>
              @if(auth()->user()->hasPermissionTo(33))
                <a href="#">
                  <button class="btn btn-warning btn-sm" data-id="{{$service->id}}" data-name="{{$service->name}}"  data-service_category_id="{{$service->service_category_id}}" 
                    type="button" data-toggle="modal" data-target="#editModal">Edit</button>
                </a>
              @endif
              @if(auth()->user()->hasPermissionTo(34))
                <a href="#">
                  <button class="btn btn-danger btn-sm" data-id="{{$service->id}}" data-name="{{$service->name}}" type="button" data-toggle="modal" data-target="#deleteModal" > Delete</button>
                </a>
              @endif
          </td>
        </tr>
        @endforeach
        
      </tbody>
    </table>
    
  </div>
  <!-- /.box-body -->
</div>
<!-- /.box -->
@endsection 

@include('masters.hospital_services.create')
@include('masters.hospital_services.edit')
@include('masters.hospital_services.delete')



@push('bk_script')


<script>

  $(document).ready(function(){
      $('#table1').DataTable( {
        "paging":   true,
        "ordering": true,
        "info":     true
      } );


  });

  $('#editModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget)
      var modal = $(this)
      
      modal.find('.modal-body #id1').val(button.data('id'))
      modal.find('.modal-body #name1').val(button.data('name'))
      $("#service_category_id1").val(button.data('service_category_id')).change();

    });
    
    $('#deleteModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget) 
      var id = button.data('id')
      var message =  "Are you sure you want to delete '".concat(button.data('name'), "' service?") ;
      var modal = $(this)
      modal.find('.modal-body #message').text(message);
      modal.find('.modal-body #service_id').val(id)
    })


</script>
@endpush