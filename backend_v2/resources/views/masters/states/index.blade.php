@extends('layouts.master')


@section('content-title')
    States
    @if (auth()->user()->hasPermissionTo(48))
        <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addModal">
            Add State
        </button>
    @endif
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <table id="table1" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Short Code </th>
                        <th>Numeric Code </th>
                        <th>Action </th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($states as $st)
                        <tr>
                            <td>{{ $st->name }}</td>
                            <td>{{ $st->short_code }}</td>
                            <td>{{ $st->num_code }}</td>

                            <td>
                                @if (auth()->user()->hasPermissionTo(49))
                                    <a href="#">
                                        <button class="btn btn-warning btn-sm" data-id="{{ $st->id }}"
                                            data-name="{{ $st->name }}" data-code="{{ $st->short_code }}"
                                            data-num_code="{{ $st->num_code }}" type="button" data-toggle="modal"
                                            data-target="#editModal">Edit</button>
                                    </a>
                                @endif
                                @if (auth()->user()->hasPermissionTo(50))
                                    <a href="#">
                                        <button class="btn btn-danger btn-sm" data-id="{{ $st->id }}"
                                            data-name="{{ $st->name }}" type="button" data-toggle="modal"
                                            data-target="#deleteModal"> Delete</button>
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

@include('masters.states.create')
@include('masters.states.edit')
@include('masters.states.delete')

@push('bk_script')
    @include('partials.notification')

    <script>
        $(document).ready(function() {
            $('#table1').DataTable({
                "paging": true,
                "ordering": true,
                "info": true
            });
        });


        $('#editModal').on('show.bs.modal', function(event) {
            $('#name1').focus();

            var button = $(event.relatedTarget)

            var modal = $(this)

            modal.find('.modal-body #id').val(button.data('id'))
            modal.find('.modal-body #name1').val(button.data('name'))
            modal.find('.modal-body #short_code1').val(button.data('code'))
            modal.find('.modal-body #num_code1').val(button.data('num_code'))

        });

        $('#deleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var message = "Are you sure you want to delete '".concat(button.data('name'), "' State?");
            var modal = $(this)
            modal.find('.modal-body #message').text(message);
            modal.find('.modal-body #state_id').val(id)
        })
    </script>
@endpush
