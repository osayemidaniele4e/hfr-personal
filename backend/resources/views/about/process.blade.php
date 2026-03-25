@extends('layouts.master')

@section('content-title')
    About Us - Process
    @if (auth()->user()->hasPermissionTo(22))
        <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addModal">
            Add Process Item
        </button>
    @endif
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <form action="{{ route('process.update') }}" method="post" enctype="multipart/form-data">

                @csrf

                @method('PUT')

                <div class="modal-body">


                    <div class="mb-5">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" aria-describedby=""
                            value="{{ $aboutProcess ? $aboutProcess->title : '' }}" name="title">
                    </div>
                    <br>

                    <div class="mb-3">
                        <label for="content" class="form-label">Description</label>
                        <div id="content">{!! $aboutProcess ? $aboutProcess->content : '' !!}</div> <!-- Quill Editor -->
                        <input type="hidden" name="content" id="hiddenContent"> <!-- Hidden input -->
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>

        </div>
        <!-- /.box-body -->
    </div>


    <div class="box">
        <div class="box-body">
            <table id="table1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Title</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($aboutProcessItems as $item)
                        <tr id="record-{{ $item->id }}">
                            <td>{{ $i++ }}</td>

                            {{-- <td style="word-wrap: break-word; white-space: normal;">{{ $item->title }}</td> --}}

                            <td style="word-wrap: break-word; white-space: normal;">
                                {!! implode(' ', array_slice(explode(' ', $item->title), 0, 20)) !!}
                                @if (str_word_count($item->title) > 6)
                                    <a href="#" data-toggle="modal" class="text-danger"
                                        data-target="#readMoreModal-{{ $item->id }}">... Read
                                        More</a>
                                @endif
                            </td>

                            <td>
                                {{-- @if (auth()->user()->hasPermissionTo(23)) --}}
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                    data-target="#editModal{{ $item->id }}">
                                    Edit
                                </button>


                                {{-- @endif --}}
                                {{-- @if (auth()->user()->hasPermissionTo(24)) --}}
                                <a href="#">
                                    <button class="btn btn-danger btn-sm" data-id="{{ $item->id }}" id="deleteBtn"
                                        data-title="{{ $item->title }}" type="button" data-toggle="modal"
                                        data-target="#deleteModal"> Delete</button>
                                </a>
                                {{-- @endif --}}
                            </td>
                        </tr>

                        <div class="modal fade" id="readMoreModal-{{ $item->id }}" tabindex="-1"
                            aria-labelledby="exampleModalScrollable2" data-bs-keyboard="false" aria-hidden="true">
                            <!-- Scrollable modal -->
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title" id="staticBackdropLabel2">Process Item
                                        </h6>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                    </div>

                                    <div class="modal-body">
                                        {!! $item->title !!}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1"
                            aria-labelledby="addModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-center">
                                <div class="modal-content">

                                    <form action="{{ route('process-item.update') }}" method="post"
                                        enctype="multipart/form-data">

                                        @csrf

                                        @method('PUT')

                                        <input type="hidden" name="id" value="{{ $item->id }}">

                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addModalLabel">Edit Process Item </h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        </div>
                                        <div class="modal-body">


                                            <div class="mb-5">
                                                <label for="title" class="form-label">Title</label>
                                                <input type="text" class="form-control" id="title"
                                                    aria-describedby="" name="title" value="{{ $item->title }}">
                                            </div>
                                            <br>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"
                                                aria-label="Close"><span aria-hidden="true">Close</button>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    @endforeach

                </tbody>
            </table>
        </div>
        <!-- /.box-body -->
    </div>


    <!-- Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-center">
            <div class="modal-content">

                <form id="addForm" method="post" enctype="multipart/form-data">

                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Add Process Item </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-5">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" aria-describedby=""
                                name="title">
                        </div>
                        <br>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <!-- /.box -->
@endsection



@section('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endsection


@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <script>
        // Initialize Quill editor
        var quill = new Quill('#content', {
            theme: 'snow',
            placeholder: 'Write the content here...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    ['strike', 'blockquote'],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    [{
                        'indent': '-1'
                    }, {
                        'indent': '+1'
                    }],
                    [{
                        'align': []
                    }],
                    ['link', 'image', 'video'],
                    ['code', 'code-block'],
                    [{
                        'font': []
                    }],
                    [{
                        'size': ['small', 'normal', 'large', 'huge']
                    }],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    [{
                        'header': '1'
                    }, {
                        'header': '2'
                    }, {
                        'header': '3'
                    }]
                ]
            }
        });

        // Set initial content from the database
        // var initialContent = `{!! addslashes($aboutProcess->content) !!}`;
        // Check if $aboutOrigin exists before using its content
        var initialContent = `{!! isset($aboutProcess) ? addslashes($aboutProcess->content) : '' !!}`;

        quill.root.innerHTML = initialContent;

        // Set the hidden input immediately after Quill is initialized
        document.getElementById("hiddenContent").value = initialContent;

        // Update hidden input when Quill content changes
        quill.on('text-change', function() {
            document.getElementById("hiddenContent").value = quill.root.innerHTML;
        });

        // Ensure hidden input has the latest content before submitting
        document.querySelector("form").addEventListener("submit", function() {
            document.getElementById("hiddenContent").value = quill.root.innerHTML;
        });
    </script>


    <script>
        $(document).ready(function() {
            $('#addForm').submit(function(e) {

                e.preventDefault(); // Prevent the default form submission (no page reload)

                // Create a FormData object to handle file and other form data
                let formData = new FormData(this);

                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: "{{ route('process-item.store') }}", // Adjust to your route
                    type: 'POST',
                    data: formData, // Pass the FormData object
                    contentType: false, // Important for file uploads
                    processData: false, // Prevent jQuery from automatically transforming the data
                    success: function(response) {
                        console.log("response", response); // Check response in the console

                        // Trigger SweetAlert with the success message
                        Swal.fire({
                            title: 'Success!',
                            text: response.message, // Message from backend
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });

                        // Optionally clear the form after submission
                        $('#addForm')[0].reset();

                        window.location.href = response.redirect;
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseJSON); // Log server-side validation errors
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON.message ||
                                'There was an issue with your submission.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>

    <script>
        $(document).on('click', '#deleteBtn', function(e) {
            e.preventDefault();

            // Get the ID of the record to delete
            var itemId = $(this).data('id');
            var recordName = $(this).data('title');

            // Use SweetAlert for confirmation
            Swal.fire({
                title: `Are you sure you want to delete <i class='text-danger'> ${recordName}</i> ?`,
                text: `You won't be able to revert this!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true,

            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with the AJAX delete request
                    $.ajax({
                        url: '/process-item-delete/' + itemId, // Replace with your actual route
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}', // CSRF token for security
                        },
                        success: function(response) {
                            if (response.success) {
                                // Show success message using SweetAlert
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );

                                // Optionally, remove the deleted record from the UI
                                $('#record-' + itemId)
                                    .remove(); // Assuming you have an element with id="record-<id>"
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                'Error!',
                                'There was an error deleting the record.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    </script>
@endsection
