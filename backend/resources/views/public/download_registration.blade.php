@extends('layouts.pub.master')



@section('content')
    <div class="latest-area section-padding bg-white">
        <div class="container">
            <div class="row">

                <form method="POST" action="{{ route('saveDownloadUserRecords') }}" class="form-horizontal">
                    @csrf

                    <div class="form-group row">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-9">
                            <div role="alert" class="alert alert-success">
                                Thank you for your interest in Nigeria HFR data, kindly sign our guest form to download the
                                data.
                            </div>
                        </div>

                    </div>

                    <div class="form-group row">
                        <label for="firstname" class="col-md-3 control-label">First Name<font color="red">*</font>
                        </label>

                        <div class="col-md-6">
                            <input id="firstname" type="text"
                                class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}" name="firstname"
                                value="{{ old('firstname') }}" required autofocus>

                            @if ($errors->has('firstname'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('firstname') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="name" class="col-md-3 control-label">Last Name<font color="red">*</font> </label>

                        <div class="col-md-6">
                            <input id="lastname" type="text"
                                class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}" name="lastname"
                                value="{{ old('lastname') }}" required>

                            @if ($errors->has('lastname'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('lastname') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-md-3 control-label">E-Mail Address<font color="red">*</font>
                        </label>

                        <div class="col-md-6">
                            <input id="email" type="email"
                                class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                                value="{{ old('email') }}" required>

                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="organisation" class="col-md-3 control-label">Organisation<font color="red">*</font>
                        </label>

                        <div class="col-md-6">
                            <input id="organisation" type="text"
                                class="form-control{{ $errors->has('organisation') ? ' is-invalid' : '' }}"
                                name="organisation" value="{{ old('organisation') }}" required>

                            @if ($errors->has('organisation'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('organisation') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="designation" class="col-md-3 control-label">Designation<font color="red">*</font>
                        </label>

                        <div class="col-md-6">
                            <input id="designation" type="text"
                                class="form-control{{ $errors->has('designation') ? ' is-invalid' : '' }}"
                                name="designation" value="{{ old('designation') }}" required>

                            @if ($errors->has('designation'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('designation') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="country" class="col-md-3 control-label">Country<font color="red">*</font> </label>
                        <div class="col-md-6">
                            <select class="form-control select2"
                                class="form-control{{ $errors->has('country') ? ' is-invalid' : '' }}" id="country"
                                name="country" required>

                                @include('public.countries')
                            </select>
                        </div>
                        @if ($errors->has('country'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('country') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group row">
                        <label for="purpose" class="col-md-3 control-label">Purpose<font color="red">*</font> </label>

                        <div class="col-md-6">
                            <textarea id="purpose" name="purpose" class="form-control" rows="5" placeholder="Intended usage of the data.."
                                required></textarea>
                            @if ($errors->has('purpose'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('purpose') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-6">
                            {!! NoCaptcha::renderJs() !!}
                            {!! NoCaptcha::display() !!}

                            @if ($errors->has('g-recaptcha-response'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                </span>
                            @endif
                        </div>

                    </div>


                    <div class="form-group row">
                        <div class="col-md-3"> </div>

                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success pull-right">Submit </button>
                        </div>

                    </div>


                </form>



            </div>
        </div>
    </div>
    </div>
@endsection

@push('custom_scripts')
    <script src="{{ asset('dist/js/select2.full.min.js') }}"></script>
    @include('partials.notification')

    <script>
        $(function() {
            $('.select2').select2()
        })
    </script>
@endpush
