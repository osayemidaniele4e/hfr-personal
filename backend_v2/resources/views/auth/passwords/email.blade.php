<!DOCTYPE html>
<html lang="en">

    <head>
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
                    {{-- <p class="subtitle">Login to Access your Dashboard</p> --}}

                    <form class="form" method="POST"action="{{ route('password.email') }}"
                        aria-label="{{ __('Reset Password') }}">
                        @csrf
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


                        <a class="forgot-password" href="{{ route('login') }}">
                            Login
                        </a>

                        <div>
                            <button type="submit" class="submit-btn1">{{ __('Send Password Reset Link') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        @if (session('status'))
            <script>
                Swal.fire({
                    title: 'Success!',
                    text: "{{ session('status') }}",
                    icon: 'success',
                    confirmButtonText: 'OK',
                    timer: 3000, // Optional: close after 3 seconds
                    showConfirmButton: false
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
                        html: `<ul style='text-align: center;'>
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
