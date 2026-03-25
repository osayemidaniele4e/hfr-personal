<!DOCTYPE html>
<html lang="en">

    <head>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Nigeria Health Facility Registry</title>
        <link rel="stylesheet" href="{{ asset('auth/styles/login.styles.css') }}" />

    </head>

    <body>
        <div class="container">
            <div class="wrapper">
                <div class="sidebar">
                    <img src="{{ asset('auth/SideBar.svg') }}" alt="Reset Password Illustration" />
                </div>

                <div class="form-container">
                    <div>
                        <img src="{{ asset('auth/new-logo.svg') }}" alt="Logo" class="logo" />
                    </div>

                    <h1 class="heading">Nigeria Health Facility Registry (HFR)</h1>
                    <p class="subtitle">Reset Password</p>

                    <form class="form" method="POST" action="{{ route('password.request') }}"
                        aria-label="{{ __('Reset Password') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input id="email" type="email" class="form-control" name="email"
                                value="{{ old('email') }}" placeholder="Enter your E-mail" required autofocus>
                            @if ($errors->has('email'))
                                <span class="help-block" style="color: red">
                                    {{ $errors->first('email') }}
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="new-password">Password</label>
                            <input id="password" type="password" class="form-control" name="password"
                                placeholder="Password">

                            @if ($errors->has('password'))
                                <span class="help-block" style="color: red">
                                    {{ $errors->first('password') }}
                                </span>
                            @endif
                        </div>


                        <div class="form-group">
                            <label for="password-confirm">Confirm Password</label>
                            <input id="password-confirm" type="password" class="form-control"
                                name="password_confirmation" placeholder="Password">
                        </div>

                        <a class="forgot-password" href="{{ route('login') }}">
                            Login
                        </a>

                        <div>
                            <button type="submit" class="submit-btn"> {{ __('Reset Password') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        @if (session()->has('status'))
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: "{{ session()->get('status') }}",
                        timer: 5000,
                        showConfirmButton: false
                    });
                });
            </script>
        @endif


        @if (session()->has('error'))
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: "{{ session()->get('error') }}",
                        timer: 5000,
                        showConfirmButton: false
                    });
                });
            </script>
        @endif


        @if ($errors->any())
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error!',
                        html: `<ul style='text-align: left;'>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>`,
                    });
                });
            </script>
        @endif

    </body>

</html>
