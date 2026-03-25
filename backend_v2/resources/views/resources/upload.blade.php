@extends('layouts.master')

@section('content-title')
    Upload File
@endsection

@section('content')
    <div class="panel panel-default ">
        <div class="panel-heading" role="tab" id="headingOne">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true"
                    aria-controls="collapseOne">

                </a>
            </h4>
        </div>
        <div class="panel-body">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel">

                    <div class="panel-body">
                        {{-- Display session error message (works for file upload errors) --}}
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        {{-- Display validation errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul style="margin-bottom: 0;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('savefile') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('filename') is-invalid @enderror" 
                                        name="filename" value="{{ old('filename') }}"
                                        placeholder="Enter filename" required autofocus>

                                    @error('filename')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <input type="file" class="form-control" name="resourcefile" id="resourcefile" />
                                    <small class="form-text text-muted">Maximum file size: 30MB</small>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <div class="col-md-4">

                                </div>
                                <div class="col-md-8">
                                    <button type="submit" class="btn btn-primary pull-right">
                                        Upload
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('bk_script')
    @include('partials.notification')

     <script>
        // Client-side file size validation
        document.getElementById('resourcefile').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const maxSize = 30 * 1024 * 1024; // 30MB in bytes
            
            if (file && file.size > maxSize) {
                alert('File is too large. Maximum size is 30MB.');
                e.target.value = '';
            }
        });
    </script>

@endpush
