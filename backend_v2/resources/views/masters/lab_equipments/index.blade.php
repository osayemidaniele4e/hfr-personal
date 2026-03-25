@extends("layouts.master")

@section('content-title')
Laboratory Equipments

@if(auth()->user()->hasPermissionTo(40)) 
  <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addModal">
      Add Equipment
    </button>
@endif

@endsection

@section("content")
<div class="box">
  <div class="box-body">
      <table id="table1" class="table table-striped table-bordered" style="width:100%">
      <thead>
        <tr>
          <th>Equipment name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        
        @foreach($equips as $eq)
        <tr>
          <td>{{$eq->name}}</td>
          <td>
              @if(auth()->user()->hasPermissionTo(41))
                <a href="#">
                  <button class="btn btn-warning btn-sm" data-id="{{$eq->id}}" data-name="{{$eq->name}}" type="button" data-toggle="modal" data-target="#editModal">Edit</button>
                </a>
              @endif
              @if(auth()->user()->hasPermissionTo(42))
                <a href="#">
                  <button class="btn btn-danger btn-sm" data-id="{{$eq->id}}" data-name="{{$eq->name}}" type="button" data-toggle="modal" data-target="#deleteModal" > Delete</button>
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

@include('masters.lab_equipments.create')
@include('masters.lab_equipments.edit')
@include('masters.lab_equipments.delete')


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
      
      modal.find('.modal-body #name1').val(button.data('name'));
      modal.find('.modal-body #id').val(button.data('id'));
    })

    $('#deleteModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget) // Button that triggered the modal
      var id = button.data('id')
      var message =  "Are you sure you want to delete equipment '".concat(button.data('name'), "' ?") ;
      var modal = $(this);
      modal.find('.modal-body #message').text(message);
      modal.find('.modal-body #equip_id').val(id);
    })

  });
</script>
@endpush