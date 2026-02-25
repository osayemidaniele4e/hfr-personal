@extends('layouts.master')

@section('content-title')
    API Keys Management
@endsection

@section('styles')
    <style>
        .api-key-display {
            background: #1a1a2e;
            color: #00ff88;
            padding: 12px 18px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            word-break: break-all;
            margin: 10px 0;
        }
        .badge-active { background-color: #28a745; }
        .badge-inactive { background-color: #dc3545; }
        .badge-expired { background-color: #ffc107; color: #333; }
        .stat-box {
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .stat-box .number { font-size: 24px; font-weight: bold; color: #333; }
        .stat-box .label { font-size: 12px; color: #666; }
    </style>
@endsection

@section('content')

    {{-- Flash: Show newly generated API key --}}
    @if (session('new_api_key'))
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4><i class="fa fa-warning"></i> API Key Generated for "{{ session('new_client_name') }}"</h4>
            <p><strong>Copy this key now — it will NOT be shown again:</strong></p>
            <div class="api-key-display">{{ session('new_api_key') }}</div>
            <button class="btn btn-sm btn-default" onclick="navigator.clipboard.writeText('{{ session('new_api_key') }}')">
                <i class="fa fa-copy"></i> Copy to Clipboard
            </button>
        </div>
    @endif

    @if (session('success') && !session('new_api_key'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">External API Clients</h3>
            <div class="box-tools">
                @if (isset($pendingCount) && $pendingCount > 0)
                    <a href="{{ route('api-clients.pending') }}" class="btn btn-warning btn-sm" style="margin-right: 5px;">
                        <i class="fa fa-clock-o"></i> {{ $pendingCount }} Pending Request{{ $pendingCount > 1 ? 's' : '' }}
                    </a>
                @endif
                <a href="{{ route('api-clients.create') }}" class="btn btn-success btn-sm">
                    <i class="fa fa-plus"></i> Create New API Key
                </a>
            </div>
        </div>
        <div class="box-body">
            @if ($clients->isEmpty())
                <div class="text-center" style="padding: 40px;">
                    <i class="fa fa-key fa-3x text-muted"></i>
                    <p class="text-muted" style="margin-top: 15px;">No API clients yet. Create your first API key to get started.</p>
                    <a href="{{ route('api-clients.create') }}" class="btn btn-success">
                        <i class="fa fa-plus"></i> Create API Key
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="apiClientsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Organisation</th>
                                <th>Key (masked)</th>
                                <th>Rate Limit</th>
                                <th>Status</th>
                                <th>Requests (24h)</th>
                                <th>Last Used</th>
                                <th>Expires</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                                <tr>
                                    <td>{{ $client->id }}</td>
                                    <td><strong>{{ $client->name }}</strong></td>
                                    <td>{{ $client->email }}</td>
                                    <td>{{ $client->organisation ?? '-' }}</td>
                                    <td><code>{{ $client->api_key }}</code></td>
                                    <td>{{ $client->rate_limit }}/min</td>
                                    <td>
                                        @if ($client->hasExpired())
                                            <span class="badge badge-expired">Expired</span>
                                        @elseif ($client->is_active)
                                            <span class="badge badge-active">Active</span>
                                        @else
                                            <span class="badge badge-inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($client->request_count_24h) }}</td>
                                    <td>{{ $client->last_used_at?->diffForHumans() ?? 'Never' }}</td>
                                    <td>{{ $client->expires_at?->format('Y-m-d') ?? 'Never' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('api-clients.logs', $client->id) }}" class="btn btn-xs btn-info" title="View Logs">
                                                <i class="fa fa-bar-chart"></i>
                                            </a>

                                            <form action="{{ route('api-clients.toggle') }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $client->id }}">
                                                <button type="submit" class="btn btn-xs {{ $client->is_active ? 'btn-warning' : 'btn-success' }}"
                                                    title="{{ $client->is_active ? 'Deactivate' : 'Activate' }}"
                                                    onclick="return confirm('Are you sure?')">
                                                    <i class="fa {{ $client->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                </button>
                                            </form>

                                            <button type="button" class="btn btn-xs btn-default" title="Edit"
                                                data-toggle="modal" data-target="#editModal{{ $client->id }}">
                                                <i class="fa fa-pencil"></i>
                                            </button>

                                            <form action="{{ route('api-clients.regenerate') }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $client->id }}">
                                                <button type="submit" class="btn btn-xs btn-primary" title="Regenerate Key"
                                                    onclick="return confirm('This will invalidate the current key. Continue?')">
                                                    <i class="fa fa-refresh"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('api-clients.destroy') }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $client->id }}">
                                                <button type="submit" class="btn btn-xs btn-danger" title="Delete"
                                                    onclick="return confirm('Permanently delete this API client? This cannot be undone.')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editModal{{ $client->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('api-clients.update') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $client->id }}">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">Edit: {{ $client->name }}</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Rate Limit (requests/min)</label>
                                                        <input type="number" name="rate_limit" class="form-control"
                                                            value="{{ $client->rate_limit }}" min="10" max="1000">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Expiry Date</label>
                                                        <input type="date" name="expires_at" class="form-control"
                                                            value="{{ $client->expires_at?->format('Y-m-d') }}">
                                                        <small class="text-muted">Leave empty for no expiry</small>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Description</label>
                                                        <textarea name="description" class="form-control" rows="3">{{ $client->description }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#apiClientsTable').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 25,
            });
        });
    </script>
@endsection
