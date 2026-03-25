<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

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

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="tokenForm">
                        @csrf
                        <div class="form-group">
                            <label for="token">Enter Token</label>
                            {{-- <input type="text" name="token" id="token"> --}}
                            <input type="text" name="token" id="token"
                                value="{{ request('token') ?? old('token') }}">
                            <div id="error" class="error"></div>
                        </div>
                        <button type="submit" class="submit-btn">Submit</button>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


        @if (session('toast_success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: @json(session('toast_success')),
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                });
            </script>
        @endif


        <script>
            document.getElementById('tokenForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const token = document.getElementById('token').value;
                const csrfToken = document.querySelector('input[name="_token"]').value;

                fetch("{{ route('verifyToken') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({
                            token
                        })
                    })
                    .then(async (res) => {
                        const data = await res.json();

                        if (res.ok && data.redirect) {
                            localStorage.setItem("verified", "true");
                            // setTimeout(() => {
                            //     window.location.href = data.redirect;
                            // }, 5000); // give localStorage time to persist
                            window.location.href = data.redirect;
                        } else if (data.errors && data.errors.token) {
                            document.getElementById("error").innerText = data.errors.token[0];
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: data.errors.token[0],
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                            });

                            setTimeout(() => {
                                window.location.href = data.redirect1;
                            }, 2000);
                        } else if (data.error) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: data.error,
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                            });
                            setTimeout(() => {
                                window.location.href = data.redirect1;
                            }, 2000);
                        }
                    })
                    .catch(err => {
                        console.error("Error:", err);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: "Unexpected error occurred.",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    });
            });
        </script>
    </body>

</html>
