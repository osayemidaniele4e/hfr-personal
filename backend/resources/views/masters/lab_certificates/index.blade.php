@extends("layouts.master")

@section('content-title')
Laboratory Certifications

@if(auth()->user()->hasPermissionTo(44))
  <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addModal">
    Add Certification
  </button>  
@endif

@endsection

@section("content")
<div class="box">
  <div class="box-body">
      <table id="table1" class="table table-striped table-bordered" style="width:100%">
      <thead>
        <tr>
          <th>Certification Type</th>
          <th>Certification Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        
        @foreach($certificates as $cert)
        <tr>
          <td>{{$cert->type}}</td>
          <td>{{$cert->name}}</td>

          <td>
              @if(auth()->user()->hasPermissionTo(45))
                <a href="#">
                  <button class="btn btn-warning btn-sm" data-id="{{$cert->id}}" data-name="{{$cert->name}}" data-type="{{$cert->type}}" type="button" data-toggle="modal" data-target="#editModal">Edit</button>
                </a>
              @endif
              @if(auth()->user()->hasPermissionTo(46))
                <a href="#">
                  <button class="btn btn-danger btn-sm" data-id="{{$cert->id}}" data-name="{{$cert->name}}" type="button" data-toggle="modal" data-target="#deleteModal" > Delete</button>
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

@include('masters.lab_certificates.create')
@include('masters.lab_certificates.edit')
@include('masters.lab_certificates.delete')


@push('bk_script')
@include('partials.notification')

<script>

  $(document).ready(function(){
    $('#table1').DataTable( {
      "paging":   true,
      "ordering": true,
      "info":     true
    } );

    $('#editModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget) // Button that triggered the modal
      var modal = $(this)

      modal.find('.modal-body #id').val(button.data('id'));      
      modal.find('.modal-body #name1').val(button.data('name'));
      $("#type1").val(button.data('type')).change();
    })

    $('#deleteModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget) // Button that triggered the modal
      var id = button.data('id')
      var message =  "Are you sure you want to delete '".concat(button.data('name'), "' certification?") ;
      var modal = $(this);
      modal.find('.modal-body #message').text(message);
      modal.find('.modal-body #certification_id').val(id);
    })

  });
</script>
@endpush