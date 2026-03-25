@extends('layouts.pub.master')


@section('content-title')
@endsection

@section('content')
    <div class="contact-form-area section-padding">
        <div class="container">
            <div role="alert" class="alert alert-success">
                <strong>Public Resources</strong>
            </div>
            <table id="table1" class="table " style="width:100%">
                <thead>
                    <tr>

                    </tr>
                </thead>
                <tbody>

                    @foreach ($resources as $res)
                        <tr>
                            <td>
                                @if ($res->format == 'pdf')
                                    <img class="center-block" src="{{ asset('img/pdf.png') }}" />
                                @endif
                                @if ($res->format == 'doc' or $res->format == 'docx')
                                    <img class="center-block" src="{{ asset('img/word.png') }}" />
                                @endif
                                @if ($res->format == 'xls' or $res->format == 'xlsx')
                                    <img class="center-block" src="{{ asset('img/excel.png') }}" />
                                @endif
                            </td>
                            <td>{{ $res->description }}</td>
                            <td>
                                <a href="{{ route('downloadFile', $res->filename) }}">
                                    <button class="btn btn-success btn-sm" type="button"> Download</button>
                                </a>

                            </td>
                        </tr>
                    @endforeach
                    <form method="POST" action="{{ route('updateuser') }}">

                    </form>
                </tbody>

            </table>
        </div>
    </div>
@endsection


@push('custom_scripts')
@endpush
