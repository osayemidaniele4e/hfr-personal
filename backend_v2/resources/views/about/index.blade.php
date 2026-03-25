@extends('layouts.master')

@section('content-title')
    About Us - Origin
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <form action="{{ route('about-us.update') }}" method="post" enctype="multipart/form-data">

                @csrf

                @method('PUT')

                <div class="modal-body">


                    <div class="mb-5">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" aria-describedby=""
                            value="{{ $aboutOrigin ? $aboutOrigin->title : '' }}" name="title">
                    </div>
                    <br>

                    <div class="mb-3">
                        <label for="content" class="form-label">Description</label>
                        <div id="content">{!! $aboutOrigin ? $aboutOrigin->content : '' !!}</div> <!-- Quill Editor -->
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
        // var initialContent = `{!! addslashes($aboutOrigin->content) !!}`;
        // Check if $aboutOrigin exists before using its content
        var initialContent = `{!! isset($aboutOrigin) ? addslashes($aboutOrigin->content) : '' !!}`;

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
@endsection
