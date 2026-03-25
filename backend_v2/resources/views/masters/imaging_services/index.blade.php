@extends("layouts.master")

@section('content-title')
Imaging Services

@if(auth()->user()->hasPermissionTo(36))
    <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addModal">
      Add Service
    </button>
@endif

@endsection

@section("content")
<div class="box">
  <div class="box-body">
    <table id="table1" class="table table-striped table-bordered" style="width:100%">
      <thead>
        <tr>
          <th>Service name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        
        @foreach($services as $service)
        <tr>
          <td>{{$service->name}}</td>
          
          <td>
            @if(auth()->user()->hasPermissionTo(37))
              <a href="#">
                <button class="btn btn-warning btn-sm" data-id="{{$service->id}}" data-service="{{$service->name}}" type="button" data-toggle="modal" data-target="#editModal">Edit</button>
              </a>
            @endif
            @if(auth()->user()->hasPermissionTo(38))
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


@include('masters.imaging_services.create')
@include('masters.imaging_services.edit')
@include('masters.imaging_services.delete')

@push('bk_script')
@include('partials.notification')

<script>
  $(document).ready(function(){
    $('#table1').DataTable( {
      "paging":   true,
      "ordering": true,
      "info":     true
    } );

    $('#addModal').on('show.bs.modal', function (event) {
      $('#name').focus();
    });

   $('#editModal').on('show.bs.modal', function (event) {
      $('#service_name1').focus();

      var button = $(event.relatedTarget)
      var id = button.data('id')
      var service=button.data('service')
      
      var modal = $(this)
      
      modal.find('.modal-body #id').val(id)
      modal.find('.modal-body #service_name1').val(service)
    });
    
    $('#deleteModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget) 
      
      var id = button.data('id')
      var message =  "Are you sure you want to delete '".concat(button.data('name'), "' service?") ;
      var modal = $(this)
      modal.find('.modal-body #message').text(message);
      modal.find('.modal-body #id').val(id)
    })

  });

  </script>
@endpush