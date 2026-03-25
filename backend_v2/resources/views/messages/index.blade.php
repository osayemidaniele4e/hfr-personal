@extends('layouts.master')


@section('content-title')
    Users Feedback
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
                            <th>From</th>
                            <th>E-mail</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($message as $m)
                            <tr>

                                <td class="mailbox-name">{{ $m->full_name }}</td>
                                <td class="mailbox-name">{{ $m->email }}</td>
                                <td class="mailbox-subject"><b>{{ $m->subject }}</b>
                                    - {{ $m->message }}
                                </td>
                                {{-- <td class="mailbox-subject"><b>{{$m->subject}}</b> -  {{$truncated = str_limit($m->message, 30, ' ...')}}</td> --}}

                                <td> {{ $m->created_at->diffForHumans() }}</td>
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
                "info": true
            });

        });
    </script>
@endpush
