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
                    <p class="subtitle">Please change your password to proceed</p>

                    <form method="POST" action="{{ route('newuser.ChangePassword') }}"
                        aria-label="{{ __('Change Password') }}">
                        @csrf


                        <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="email">Password</label>

                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                <input id="password" type="password" class="form-control" name="password"
                                    placeholder="New Password" required>
                            </div>
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    {{ $errors->first('password') }}
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="email">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                <input id="password-confirm" type="password" class="form-control"
                                    name="password_confirmation" placeholder="Retype New Password" required>
                            </div>
                        </div>


                        <a class="forgot-password" href="{{ route('login') }}">
                            Login
                        </a>

                        <div>
                            <button type="submit" class="submit-btn1">
                                {{ __('Change Password') }}
                            </button>
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
