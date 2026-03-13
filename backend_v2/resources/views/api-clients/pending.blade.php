@extends('layouts.master')

@section('content-title')
    Pending API Key Requests
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
        .use-case-text {
            max-width: 300px;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-size: 13px;
            color: #555;
        }
        .badge-pending { background-color: #f0ad4e; }
        .badge-rejected { background-color: #dc3545; }
        .badge-approved { background-color: #28a745; }
    </style>
@endsection

@section('content')

    {{-- Flash: Show newly generated API key --}}
    @if (session('new_api_key'))
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4><i class="fa fa-warning"></i> API Key Generated for "{{ session('new_client_name') }}"</h4>
            <p><strong>The key has been emailed. A backup copy is below:</strong></p>
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

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    {{-- Pending Requests --}}
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-clock-o"></i> Pending Requests ({{ $pendingClients->count() }})</h3>
        </div>
        <div class="box-body">
            @if ($pendingClients->isEmpty())
                <div class="text-center" style="padding: 40px;">
                    <i class="fa fa-check-circle fa-3x text-success"></i>
                    <p class="text-muted" style="margin-top: 15px;">No pending API key requests. All caught up!</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="pendingTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Organisation</th>
                                <th>Use Case</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingClients as $client)
                                <tr>
                                    <td>{{ $client->id }}</td>
                                    <td><strong>{{ $client->name }}</strong></td>
                                    <td>{{ $client->email }}</td>
                                    <td>{{ $client->organisation ?? '-' }}</td>
                                    <td>
                                        <div class="use-case-text">{{ Str::limit($client->use_case, 150) }}</div>
                                        @if(strlen($client->use_case) > 150)
                                            <button class="btn btn-xs btn-link" data-toggle="modal" data-target="#useCase{{ $client->id }}">
                                                Read more
                                            </button>
                                        @endif
                                    </td>
                                    <td>{{ $client->created_at->diffForHumans() }}<br><small class="text-muted">{{ $client->created_at->format('d M Y H:i') }}</small></td>
                                    <td>
                                        @if (auth()->user()->hasPermissionTo(79))
                                        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveModal{{ $client->id }}">
                                            <i class="fa fa-check"></i> Approve
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal{{ $client->id }}">
                                            <i class="fa fa-times"></i> Reject
                                        </button>
                                        @else
                                        <span class="text-muted">No permission</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Use Case Modal --}}
                                <div class="modal fade" id="useCase{{ $client->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">Use Case — {{ $client->name }}</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p style="white-space: pre-wrap;">{{ $client->use_case }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Approve Modal --}}
                                <div class="modal fade" id="approveModal{{ $client->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('api-clients.approve') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $client->id }}">
                                                <div class="modal-header" style="background:#28a745; color:#fff;">
                                                    <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                                                    <h4 class="modal-title"><i class="fa fa-check"></i> Approve: {{ $client->name }}</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <p>An API key will be generated and emailed to <strong>{{ $client->email }}</strong>.</p>
                                                    <div class="form-group">
                                                        <label>Rate Limit (requests/min)</label>
                                                        <input type="number" name="rate_limit" class="form-control" value="60" min="10" max="1000">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Expiry Date (optional)</label>
                                                        <input type="date" name="expires_at" class="form-control" value="{{ now()->addYear()->format('Y-m-d') }}">
                                                        <small class="text-muted">Defaults to 1 year from today. Clear to set no expiry.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Approve &amp; Send Key</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Reject Modal --}}
                                <div class="modal fade" id="rejectModal{{ $client->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('api-clients.reject') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $client->id }}">
                                                <div class="modal-header" style="background:#dc3545; color:#fff;">
                                                    <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                                                    <h4 class="modal-title"><i class="fa fa-times"></i> Reject: {{ $client->name }}</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <p>This will reject the request and notify <strong>{{ $client->email }}</strong>.</p>
                                                    <div class="form-group">
                                                        <label>Reason (optional — will be included in the email)</label>
                                                        <textarea name="rejection_reason" class="form-control" rows="3"
                                                            placeholder="e.g. Insufficient use case description, please resubmit with more details."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger"><i class="fa fa-times"></i> Reject Request</button>
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

    {{-- Recently Rejected --}}
    @if ($rejectedClients->isNotEmpty())
        <div class="box box-danger collapsed-box">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-times-circle"></i> Recently Rejected ({{ $rejectedClients->count() }})</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Organisation</th>
                                <th>Rejection Reason</th>
                                <th>Reviewed By</th>
                                <th>Reviewed At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rejectedClients as $client)
                                <tr>
                                    <td>{{ $client->name }}</td>
                                    <td>{{ $client->email }}</td>
                                    <td>{{ $client->organisation ?? '-' }}</td>
                                    <td>{{ $client->rejection_reason ?? '-' }}</td>
                                    <td>{{ $client->reviewer?->name ?? 'System' }}</td>
                                    <td>{{ $client->reviewed_at?->format('d M Y H:i') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#pendingTable').DataTable({
                responsive: true,
                order: [[5, 'asc']],
                pageLength: 25,
                columnDefs: [
                    { responsivePriority: 1, targets: 1 },  // Name
                    { responsivePriority: 2, targets: -1 },  // Actions (last column — always visible)
                    { responsivePriority: 3, targets: 2 },  // Email
                    { responsivePriority: 10001, targets: 4 } // Use Case (collapse first)
                ]
            });
        });
    </script>
@endsection
