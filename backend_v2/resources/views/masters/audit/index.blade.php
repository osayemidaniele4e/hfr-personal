@extends('layouts.master')


@section('content-title')
    Audit Trail
@endsection

@section('content')
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

            <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Event</th>
                            <th>Facility</th>
                            <th>IP Address</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($audits as $m)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td class="mailbox-name">
                                    {{ $m->lastname }} {{ $m->firstname }}
                                </td>
                                <td class="mailbox-name">{{ $m->event }}</td>
                                <td class="mailbox-name">{{ $m->facility_name }}</td>
                                <td class="mailbox-name">{{ $m->ip_address }}</td>

                                <td>{{ \Carbon\Carbon::parse($m->created_at)->format('F j, Y h:i A') }}</td>

                            </tr>
                        @endforeach

                    </tbody>
                </table>
                <!-- /.table -->
            </div>

        </div>
        <!-- /.box-body -->
        <div class="box-footer">

        </div>
    </div>
    <!-- /. box -->
@endsection


@push('bk_script')
    <script>
        $(document).ready(function() {
            $('#table1').DataTable({
                "paging": true,
                "ordering": true,
                "info": true,
                "pageLength": 100
            });

        });
    </script>
@endpush
