@extends('layouts.master')

@section('content-title')
    User Roles

    @if (auth()->user()->hasPermissionTo(22))
        <a href="{{ route('roles.create') }}">
            <button type="button" class="btn btn-primary pull-right">
                Add User Role
            </button>
        </a>
    @endif
@endsection

@section('content')
    <div class="box">
        <div class="box-body">

            <table id="table1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Description</th>
                        {{-- <th>Permissions</th> --}}
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->description }}</td>

                            <td>
                                @if (in_array($role->id, subRoles()))
                                    @if (auth()->user()->hasPermissionTo(23))
                                        <a href="{{ route('roles.edit', $role->id) }}">
                                            <button class="btn btn-warning btn-sm" type="button">Edit</button>
                                        </a>
                                    @endif
                                    @if (auth()->user()->hasPermissionTo(24))
                                        <a href="#">
                                            <button class="btn btn-danger btn-sm" data-id="{{ $role->id }}"
                                                data-name="{{ $role->name }}" type="button" data-toggle="modal"
                                                data-target="#deleteModal"> Delete</button>
                                        </a>
                                    @endif
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



@include('roles.delete')

@push('bk_script')
    @include('partials.notification')

    <script>
        $(document).ready(function() {
            $('#table1').DataTable({
                "paging": true,
                "ordering": true,
                "info": true
            });



            $('#deleteModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget)
                var id = button.data('id')
                var message = "Are you sure you want to delete '".concat(button.data('name'), "' Role?");
                var modal = $(this)
                modal.find('.modal-body #message').text(message);
                modal.find('.modal-body #role_id').val(id)
            })



        });
    </script>
@endpush
