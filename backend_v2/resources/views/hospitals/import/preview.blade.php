@extends('layouts.master')

@section('content-title')
    Import Preview
    <a href="{{ route('hospitals.import.index') }}">
        <button type="button" class="btn btn-default pull-right">
            <i class="fa fa-arrow-left"></i> Back to Import
        </button>
    </a>
@endsection

@section('content')

    {{-- Summary Box --}}
    <div class="row">
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-database"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Records</span>
                    <span class="info-box-number">{{ $totalCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Valid Records</span>
                    <span class="info-box-number">{{ $validCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Records with Errors</span>
                    <span class="info-box-number">{{ $errorCount }}</span>
                </div>
            </div>
        </div>
    </div>

    @if ($errorCount > 0)
        <div class="callout callout-danger">
            <h4><i class="fa fa-exclamation-triangle"></i> Validation Errors Found</h4>
            <p>{{ $errorCount }} record(s) have validation errors (highlighted in red). Please edit or remove these records before submitting.</p>
        </div>
    @endif

    {{-- Search & Filter --}}
    <div class="box box-default">
        <div class="box-body">
            <form method="GET" action="{{ route('hospitals.import.preview', $batchId) }}" class="form-inline">
                <div class="form-group" style="margin-right:10px;">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search facility name, location, state, LGA, phone…"
                               value="{{ $search ?? '' }}" style="min-width:350px;">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Search</button>
                        </span>
                    </div>
                </div>
                <div class="form-group" style="margin-right:10px;">
                    <select name="filter_status" class="form-control" onchange="this.form.submit()">
                        <option value="">All Records</option>
                        <option value="valid" {{ ($filterStatus ?? '') === 'valid' ? 'selected' : '' }}>Valid Only</option>
                        <option value="errors" {{ ($filterStatus ?? '') === 'errors' ? 'selected' : '' }}>Errors Only</option>
                    </select>
                </div>
                @if($search || $filterStatus)
                    <a href="{{ route('hospitals.import.preview', $batchId) }}" class="btn btn-default">
                        <i class="fa fa-times"></i> Clear
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Batch: <small>{{ Str::limit($batchId, 12, '...') }}</small></h3>
            <div class="box-tools">
                @if ($errorCount === 0)
                    <form action="{{ route('hospitals.import.submit', $batchId) }}" method="POST" style="display:inline;"
                        onsubmit="return confirm('Are you sure you want to submit and publish all {{ $totalCount }} records? This will create hospital records directly without requiring approval.')">
                        @csrf
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="fa fa-paper-plane"></i> Submit &amp; Publish All ({{ $totalCount }}) Records
                        </button>
                    </form>
                @else
                    <button type="button" class="btn btn-success" disabled title="Fix all errors before submitting">
                        <i class="fa fa-paper-plane"></i> Submit &amp; Publish All Records
                    </button>
                @endif
                <form action="{{ route('hospitals.import.destroyBatch', $batchId) }}" method="POST"
                    style="display:inline;" onsubmit="return confirm('Delete entire batch?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash"></i> Delete Batch
                    </button>
                </form>
            </div>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Status</th>
                        <th>Facility Name</th>
                        <th>Start Date</th>
                        <th>State</th>
                        <th>LGA</th>
                        <th>Ward</th>
                        <th>Ownership</th>
                        <th>Facility Level</th>
                        <th>Lat / Lng</th>
                        <th>Errors</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $index => $record)
                        <tr class="{{ !$record->is_valid ? 'danger' : '' }}">
                            <td>{{ $records->firstItem() + $index }}</td>
                            <td>
                                @if ($record->is_valid)
                                    <span class="label label-success">Valid</span>
                                @else
                                    <span class="label label-danger">Errors</span>
                                @endif
                            </td>
                            <td>{{ $record->facility_name }}</td>
                            <td>{{ $record->start_date ? \Carbon\Carbon::parse($record->start_date)->format('Y-m-d') : '-' }}</td>
                            <td>{{ $states[$record->state_id] ?? $record->state_id }}</td>
                            <td>{{ $lgas[$record->lga_id] ?? $record->lga_id }}</td>
                            <td>{{ $wards[$record->ward_id] ?? $record->ward_id }}</td>
                            <td>{{ $ownerships[$record->ownership_id] ?? $record->ownership_id }}</td>
                            <td>{{ $facilityLevels[$record->facility_level_id] ?? $record->facility_level_id }}</td>
                            <td>
                                @if ($record->latitude && $record->longitude)
                                    {{ number_format($record->latitude, 4) }},
                                    {{ number_format($record->longitude, 4) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($record->validation_errors)
                                    @php $errs = json_decode($record->validation_errors, true); @endphp
                                    <button type="button" class="btn btn-xs btn-danger" data-toggle="popover"
                                        data-trigger="hover" data-placement="left" data-html="true"
                                        data-content="<ul class='list-unstyled' style='margin:0;padding:0;'>@foreach($errs as $err)<li>• {{ $err }}</li>@endforeach</ul>">
                                        {{ count($errs) }} error(s)
                                    </button>
                                @else
                                    <span class="text-success"><i class="fa fa-check"></i></span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('hospitals.import.edit', [$batchId, $record->id]) }}"
                                    class="btn btn-xs btn-warning" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <form action="{{ route('hospitals.import.destroy', [$batchId, $record->id]) }}"
                                    method="POST" style="display:inline;"
                                    onsubmit="return confirm('Remove this record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="box-footer">
            <div class="row">
                <div class="col-md-6">
                    Showing {{ $records->firstItem() }} to {{ $records->lastItem() }} of {{ $records->total() }} records
                </div>
                <div class="col-md-6">
                    <div class="pull-right">
                        {{ $records->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('bk_script')
    @include('partials.notification')
    <script>
        $(document).ready(function() {
            $('[data-toggle="popover"]').popover();

            $('#submitBtn').closest('form').on('submit', function() {
                $('#submitBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');
            });
        });
    </script>
@endpush
