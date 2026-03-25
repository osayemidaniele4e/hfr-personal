@extends('layouts.master')

@section('content-title')
    Import Hospitals
    <a href="{{ route('hospitals.index') }}">
        <button type="button" class="btn btn-default pull-right" style="margin-left: 10px;">
            <i class="fa fa-arrow-left"></i> Back to Hospitals
        </button>
    </a>
    <a href="{{ route('hospitals.import.template') }}">
        <button type="button" class="btn btn-success pull-right">
            <i class="fa fa-download"></i> Download Import Template
        </button>
    </a>
@endsection

@section('content')

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Upload Hospital Data</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="callout callout-info">
                        <h4><i class="fa fa-info-circle"></i> Instructions</h4>
                        <ol>
                            <li>Download the <strong>Import Template</strong> using the button above.</li>
                            <li>Fill in the hospital data following the sample row format. Fields marked with <strong>*</strong> are required.</li>
                            <li>Use <strong>numeric IDs</strong> for State, LGA, Ward, Ownership, Facility Level, and Status fields.</li>
                            <li>Dates should be in <strong>YYYY-MM-DD</strong> format (e.g., 2024-01-15).</li>
                            <li>Upload the completed file (Excel or CSV, max 10MB).</li>
                            <li>Review and correct any errors in the preview before final submission.</li>
                        </ol>
                    </div>

                    <form action="{{ route('hospitals.import.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('import_file') ? 'has-error' : '' }}">
                            <label for="import_file">Select File (Excel or CSV)</label>
                            <input type="file" class="form-control" id="import_file" name="import_file"
                                accept=".xlsx,.xls,.csv" required>
                            @if ($errors->has('import_file'))
                                <span class="help-block text-danger">{{ $errors->first('import_file') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block" id="uploadBtn">
                                <i class="fa fa-upload"></i> Upload and Preview
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($batches->count() > 0)
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Pending Import Batches</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Batch ID</th>
                            <th>Total Records</th>
                            <th>Valid Records</th>
                            <th>Uploaded At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($batches as $batch)
                            <tr>
                                <td><small>{{ Str::limit($batch->batch_id, 8, '...') }}</small></td>
                                <td>{{ $batch->total }}</td>
                                <td>
                                    <span class="{{ $batch->valid_count == $batch->total ? 'text-success' : 'text-danger' }}">
                                        {{ $batch->valid_count }} / {{ $batch->total }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($batch->uploaded_at)->format('d M Y, H:i') }}</td>
                                <td>
                                    <a href="{{ route('hospitals.import.preview', $batch->batch_id) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fa fa-eye"></i> Preview
                                    </a>
                                    <form action="{{ route('hospitals.import.destroyBatch', $batch->batch_id) }}"
                                        method="POST" style="display: inline;"
                                        onsubmit="return confirm('Are you sure you want to delete this entire batch?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection

@push('bk_script')
    @include('partials.notification')
    <script>
        $(document).ready(function() {
            $('#uploadBtn').closest('form').on('submit', function() {
                $('#uploadBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
            });
        });
    </script>
@endpush
