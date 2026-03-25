@extends('layouts.pub.master')


@section('content')
    <div class="contact-form-area section-padding">
        <div class="container">


            <div class="row">
                <div class="col-md-4">
                    <h4 class="contact-title">contact info</h4>
                    <div class="contact-text">
                        <h4><strong>Federal Ministry of Health <br></strong></h4>
                        <strong>Department of Health Planning Research and Statistics </strong>
                        <br>
                        <br>

                        <p>
                            <span class="c-icon"><i class="zmdi zmdi-pin"></i></span><span class="c-text">Address<br>
                                New Federal Secretariat Complex, Phase III, <br>
                                Ahmadu Bello Way, Central Business District,<br>
                                FCT Abuja, Nigeria <br>
                                <br>
                                <p><span class="c-icon"><i class="zmdi zmdi-email"></i></span><span class="c-text">
                                        Email</span></p>
                                <a href=""> hfr@health.gov.ng<br></a>

                                <br>
                                <p><span class="c-icon"><i class="zmdi zmdi-phone"></i></span><span
                                        class="c-text">Telephone</span></p>
                                +234 805 965 9211 <br>
                                +234 806 644 9855 <br>
                    </div>
                </div>


                <div class="col-md-8">

                    <h4 class="contact-title">Send us a message/ feedback</h4>

                    <form method="POST" action="{{ route('storecontact') }}" class="form-horizontal">
                        @csrf

                        <div class="form-group row">
                            <label for="firstname" class="col-md-2 control-label">Your Name<font color="red">*</font>
                            </label>

                            <div class="col-md-10">
                                <input id="full_name" type="text" name="full_name" class="form-control"
                                    value="{{ old('full_name') }}" placeholder="Full name" required autofocus>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-2 control-label">Your E-Mail<font color="red">*</font>
                            </label>

                            <div class="col-md-10">
                                <input id="email" type="email" name="email" class="form-control"
                                    value="{{ old('email') }}" placeholder="E-mail address" required>

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="subject" class="col-md-2 control-label">Subject<font color="red">*</font>
                            </label>

                            <div class="col-md-10">
                                <input id="subject" type="text" name="subject" class="form-control"
                                    value="{{ old('subject') }}" placeholder="Subject" required>


                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="message" class="col-md-2 control-label">Message<font color="red">*</font>
                            </label>

                            <div class="col-md-10">
                                <textarea id="message" name="message" class="form-control" rows="10" value="{{ old('message') }}"
                                    placeholder="Your message.." required></textarea>

                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                            <div class="col-md-2">
                            </div>
                            <div class="col-md-10">
                                {!! NoCaptcha::renderJs() !!}
                                {!! NoCaptcha::display() !!}

                                @if ($errors->has('g-recaptcha-response'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>



                        <div class="box-footer">

                            <button type="submit" class="btn btn-success pull-right">Send </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
        {{-- container --}}
    </div>
@endsection

@push('custom_scripts')
    @include('partials.notification')
@endpush
