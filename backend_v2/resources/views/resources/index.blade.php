@extends('layouts.master')


@section('content-title')
    Resources

    @if (auth()->user()->hasPermissionTo(26))
        <a href="{{ route('upload') }}">
            <button type="button" class="btn btn-primary pull-right">
                Upload Document
            </button>
        </a>
    @endif
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <table id="table1" class="table " style="width:100%">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Document Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($resources as $res)
                        <tr>
                            <td>
                                @if ($res->format == 'pdf')
                                    <img class="center-block" src="/img/pdf.png" />
                                @endif
                                @if ($res->format == 'doc' or $res->format == 'docx')
                                    <img class="center-block" src="/img/word.png" />
                                @endif
                                @if ($res->format == 'xls' or $res->format == 'xlsx')
                                    <img class="center-block" src="/img/excel.png" />
                                @endif
                            </td>
                            <td>{{ $res->description }}</td>
                            <td>
                                <a href="{{ asset($res->filename) }}" target="_blank">
                                    <button class="btn btn-success btn-sm" type="button">Download</button>
                                </a>
                                @if (auth()->user()->hasPermissionTo(27))
                                    <a href="#">
                                        <button class="btn btn-warning btn-sm" data-id="{{ $res->id }}"
                                            data-desc="{{ $res->description }}" type="button" data-toggle="modal"
                                            data-target="#edit">Edit</button>
                                    </a>
                                @endif
                                @if (auth()->user()->hasPermissionTo(28))
                                    <a href="#">
                                        <button class="btn btn-danger btn-sm" data-id="{{ $res->id }}"
                                            data-filename="{{ $res->filename }}" type="button" data-toggle="modal"
                                            data-target="#delete"> Delete</button>
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


{{--  edit resource  --}}
<div class="modal fade" id="edit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Update Resource</h4>
                <div class='notifications top-right'></div>
            </div>
            <div class="modal-body">

                <div class="panel-body">
                    <form method="POST" action="{{ route('updateResource') }}" enctype="multipart/form-data">
                        @csrf
                        <input id="id" name="id" type="hidden">

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('Document Name') }}</label>

                            <div class="col-md-12">
                                <input id="filename1" type="text" class="form-control" name="filename1" required
                                    autofocus>

                                <span class="text-danger">
                                    <strong id="filename-error1"></strong>
                                </span>
                            </div>

                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input type="file" class="form-control" name="resourcefile" />
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" id="update" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>

            </div>


        </div>
    </div>
</div><!--/.modal -->

<!-- Modal delete record -->
<div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">

            <form action="{{ route('deleteFile') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <p class="text-center">
                        Are you sure you want to delete this document?
                    </p>
                    <input type="hidden" id="doc_id" name="doc_id">
                    <input type="hidden" id="filename" name="filename">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-warning btn-sm">Yes</button>
                </div>
            </form>

        </div>
    </div>
</div>


@push('bk_script')
    @include('partials.notification')

    <script>
        $(document).ready(function() {
            $('#table1').DataTable({
                "paging": true,
                "ordering": false,
                "lengthChange": false,
                "searching": false,
                "autoWidth": false,
            });


            //edit modal form
            $('#edit').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget)
                var desc = button.data('desc')
                var id = button.data('id')
                var modal = $(this)

                modal.find('.modal-body #filename1').val(desc);
                modal.find('.modal-body #id').val(id);
            }); //end edit

            //delete resource modal
            $('#delete').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget)
                var id = button.data('id')
                var filename = button.data('filename')
                var modal = $(this)

                modal.find('.modal-body #doc_id').val(id);
                modal.find('.modal-body #filename').val(filename);

            }); //end


        });
    </script>
@endpush
