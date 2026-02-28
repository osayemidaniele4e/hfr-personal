@extends('layouts.master')

@section('content-title')
    API Usage Logs — {{ $client->name }}
@endsection

@section('styles')
    <style>
        .stat-card { text-align: center; padding: 15px; background: #fff; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 15px; }
        .stat-card .number { font-size: 28px; font-weight: 700; }
        .stat-card .label-text { font-size: 12px; color: #888; text-transform: uppercase; }
        .status-2xx { color: #28a745; }
        .status-4xx { color: #ffc107; }
        .status-5xx { color: #dc3545; }
    </style>
@endsection

@section('content')
    <a href="{{ route('api-clients.index') }}" class="btn btn-default btn-sm" style="margin-bottom: 15px;">
        <i class="fa fa-arrow-left"></i> Back to API Clients
    </a>

    {{-- Client Info --}}
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Client Details</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-3"><strong>Name:</strong> {{ $client->name }}</div>
                <div class="col-md-3"><strong>Email:</strong> {{ $client->email }}</div>
                <div class="col-md-3"><strong>Organisation:</strong> {{ $client->organisation ?? '-' }}</div>
                <div class="col-md-3"><strong>Rate Limit:</strong> {{ $client->rate_limit }}/min</div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row">
        <div class="col-md-2">
            <div class="stat-card">
                <div class="number">{{ number_format($stats['total_requests']) }}</div>
                <div class="label-text">Total Requests</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="number">{{ number_format($stats['today']) }}</div>
                <div class="label-text">Today</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="number">{{ number_format($stats['this_week']) }}</div>
                <div class="label-text">This Week</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="number">{{ $stats['avg_response_time'] ? round($stats['avg_response_time']) . 'ms' : '-' }}</div>
                <div class="label-text">Avg Response</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="number">{{ number_format($stats['error_rate']) }}</div>
                <div class="label-text">Errors (4xx/5xx)</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="number">{{ $stats['top_endpoints']->count() }}</div>
                <div class="label-text">Unique Endpoints</div>
            </div>
        </div>
    </div>

    {{-- Top Endpoints --}}
    @if ($stats['top_endpoints']->isNotEmpty())
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Top Endpoints</h3>
            </div>
            <div class="box-body">
                <table class="table table-condensed">
                    <thead>
                        <tr><th>Endpoint</th><th>Requests</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($stats['top_endpoints'] as $ep)
                            <tr><td><code>{{ $ep->endpoint }}</code></td><td>{{ number_format($ep->count) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Logs Table --}}
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Request Logs</h3>
        </div>
        <div class="box-body">
            @if ($logs->isEmpty())
                <p class="text-center text-muted">No requests logged yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Method</th>
                                <th>Endpoint</th>
                                <th>Status</th>
                                <th>Response Time</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td><span class="label label-default">{{ $log->method }}</span></td>
                                    <td><code>{{ $log->endpoint }}</code></td>
                                    <td>
                                        @php
                                            $class = 'status-2xx';
                                            if ($log->status_code >= 400) $class = 'status-4xx';
                                            if ($log->status_code >= 500) $class = 'status-5xx';
                                        @endphp
                                        <strong class="{{ $class }}">{{ $log->status_code }}</strong>
                                    </td>
                                    <td>{{ $log->response_time_ms }}ms</td>
                                    <td>{{ $log->ip_address }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-center">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
