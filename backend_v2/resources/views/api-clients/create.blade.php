@extends('layouts.master')

@section('content-title')
    Create New API Key
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">New API Client</h3>
        </div>
        <form action="{{ route('api-clients.store') }}" method="POST">
            @csrf
            <div class="box-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin-bottom: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label>Client / Application Name <span class="text-red">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                placeholder="e.g., DHIS2 Integration, Mobile App" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label>Contact Email <span class="text-red">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                placeholder="developer@example.com" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Organisation</label>
                            <input type="text" name="organisation" class="form-control" value="{{ old('organisation') }}"
                                placeholder="e.g., World Health Organization">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('rate_limit') ? 'has-error' : '' }}">
                            <label>Rate Limit (req/min) <span class="text-red">*</span></label>
                            <input type="number" name="rate_limit" class="form-control"
                                value="{{ old('rate_limit', 60) }}" min="10" max="1000" required>
                            <small class="text-muted">Default: 60. Max: 1000</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                            <small class="text-muted">Leave empty for no expiry</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description / Purpose</label>
                    <textarea name="description" class="form-control" rows="3"
                        placeholder="Describe how this API key will be used...">{{ old('description') }}</textarea>
                </div>

                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <strong>Note:</strong> After creation, the API key will be displayed <strong>only once</strong>.
                    Make sure to copy it immediately and share it securely with the client.
                </div>
            </div>
            <div class="box-footer">
                <a href="{{ route('api-clients.index') }}" class="btn btn-default">Cancel</a>
                <button type="submit" class="btn btn-success pull-right">
                    <i class="fa fa-key"></i> Generate API Key
                </button>
            </div>
        </form>
    </div>
@endsection
