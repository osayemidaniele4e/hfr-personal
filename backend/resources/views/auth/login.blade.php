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
                        <a href="{{ env('FRONTEND_URL') }}">
                            <img src="{{ asset('auth/new-logo.svg') }}" alt="Logo" class="logo" />
                        </a>
                    </div>

                    <h1 class="heading">Nigeria Health Facility Registry (HFR)</h1>
                    <p class="subtitle">Login to Access your Dashboard</p>

                    <form class="form" method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input id="email" type="email" class="form-control" name="email"
                                value="{{ old('email') }}" placeholder="E-mail" autofocus>

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

                        <a class="forgot-password" href="{{ route('password.request') }}">
                            Forgot Password
                        </a>

                        <div>
                            <button type="submit" class="submit-btn">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


        @if (session('success'))
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        title: 'Success!',
                        text: '{{ session('success') }}',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        timer: 3000,
                        showConfirmButton: false
                    });
                });
            </script>
        @endif

        @if ($errors->has('email'))
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        title: 'Error!',
                        text: '{{ $errors->first('email') }}',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        timer: 3000,
                        showConfirmButton: false
                    });
                });
            </script>
        @endif

        @if ($errors->has('password'))
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        title: 'Error!',
                        text: '{{ $errors->first('password') }}',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        timer: 3000,
                        showConfirmButton: false
                    });
                });
            </script>
        @endif



    </body>

</html>
