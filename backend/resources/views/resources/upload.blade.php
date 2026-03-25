@extends('layouts.master')

@section('content-title')
Upload File

@endsection

@section('content')

<div class="panel panel-default ">
        <div class="panel-heading" role="tab" id="headingOne">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        
                    </a>
                </h4>
        </div>
    <div class="panel-body">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel">
    
                <div class="panel-body">
                    <form method="POST" action="{{ route('savefile') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="filename" value="{{ old('filename') }}" placeholder="Enter filename" required autofocus>

                                @if ($errors->has('firstname'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('firstname') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <input type="file" name="resourcefile"/>
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
@endpush