<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nigeria Health Facility Registry</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ asset('dist/css/font-awesome/css/font-awesome.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/css/ionicons/css/ionicons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/css/AdminLTE.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/css/bootstrap-datepicker.min.css') }}" />
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('/dist/css/select2.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" />
    <link rel="stylesheet" href="{{ asset('dist/multiselect-master/css/bootstrap-multiselect.css') }}"
        type="text/css" />
    <link rel="stylesheet" href="{{ asset('dist/css/skins/skin-green.min.css') }}" />
    {{-- <link rel="stylesheet" href="{{ asset("dist/MultiSelect/jquery.multiselect.css")}}"/> --}}
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    @yield('styles')
    @yield('bk_css')
</head>

<body class="hold-transition skin-green sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">

        <header class="main-header">
            <!-- Logo -->
            <a href="{{ route('admin_home') }}" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><b>H</b>FR</span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><b>HFR </b>Administration</span>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- Messages: style can be found in dropdown.less-->
                        <li class="dropdown messages-menu">
                            {{-- <a href="{{route('home')}}">
                                            <img src="{{asset('img/home.png')}}" width="15" height="15" class="user-image" alt="HFR Home">
                                        </a> --}}

                        </li>
                        <!-- Notifications: style can be found in dropdown.less -->

                        <li class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bell-o"></i>
                                <span class="label label-warning">
                                    @if (auth()->user()->hasPermissionTo(59) or auth()->user()->hasPermissionTo(60) or auth()->user()->hasPermissionTo(61))
                                        {{ $approval_count[3] }}
                                    @endif
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                {{-- <li class="header">You have {{ auth()->user()->unreadNotifications->count() }} notifications</li> --}}
                                @if ($approval_count[3] == 0)
                                    <li class="header">You have 0 notifications</li>
                                @endif
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        <li class="header">
                                            @if (auth()->user()->unreadNotifications->count() != 0)
                                                <a href="{{ route('notification.markAsRead') }}">
                                                    <i class="fa fa-eye"></i> <strong>Mark all as Read</strong>
                                                </a>
                                            @endif

                                        </li>
                                        @if (auth()->user()->hasPermissionTo(59) and $approval_count[0] > 0)
                                            <li><a href="{{ route('verify.pending') }}">
                                                    <i class="fa fa-check-circle text-aqua"></i> You have
                                                    {{ $approval_count[0] }} pending verification(s)
                                                </a>
                                            </li>
                                        @endif
                                        @if (auth()->user()->hasPermissionTo(60) and $approval_count[1] > 0)
                                            <li><a href="{{ route('validate.pending') }}">
                                                    <i class="fa fa-check-circle text-blue"></i> You have
                                                    {{ $approval_count[1] }} pending validation(s)
                                                </a>
                                            </li>
                                        @endif
                                        @if (auth()->user()->hasPermissionTo(61) and $approval_count[2] > 0)
                                            <li><a href="{{ route('publish.pending') }}">
                                                    <i class="fa fa-check-circle text-green"></i> You have
                                                    {{ $approval_count[2] }} pending publication(s)
                                                </a>
                                            </li>
                                        @endif
                                        @foreach (auth()->user()->unreadNotifications as $notification)
                                            <li>
                                                @if ($notification->type == 'App\Notifications\CreateRequest')
                                                    <a href="">
                                                        <i class="fa fa-exclamation-circle text-aqua"></i>
                                                        {{ $notification->data['action'] }}
                                                    </a>
                                                @endif
                                                @if ($notification->type == 'App\Notifications\UpdateRequest')
                                                    <a href="">
                                                        <i class="fa fa-exclamation-circle text-blue"></i>
                                                        {{ $notification->data['action'] }}
                                                    </a>
                                                @endif
                                                @if ($notification->type == 'App\Notifications\DeleteRequest')
                                                    <a href="">
                                                        <i class="fa fa-exclamation-circle text-red"></i>
                                                        {{ $notification->data['action'] }}
                                                    </a>
                                                @endif
                                                @if ($notification->type == 'App\Notifications\FacilityApproved')
                                                    <a href="}">
                                                        <i class="fa fa-check-circle text-green"></i>
                                                        {{ $notification->data['action'] }}
                                                    </a>
                                                @endif
                                                @if ($notification->type == 'App\Notifications\FacilityVerifiedLevel1')
                                                    <a href="}">
                                                        <i class="fa fa-check-circle text-green"></i>
                                                        {{ $notification->data['action'] }}
                                                    </a>
                                                @endif
                                                @if ($notification->type == 'App\Notifications\ApprovalRejected')
                                                    <a href="#">
                                                        <i class="fa fa-times-circle text-red"></i>
                                                        {{ $notification->data['action'] }}
                                                    </a>
                                                @endif
                                                @if ($notification->type == 'App\Notifications\VerificationRejectedLevel1')
                                                    <a href="">
                                                        <i class="fa fa-times-circle text-red"></i>
                                                        {{ $notification->data['action'] }}
                                                    </a>
                                                @endif
                                                @if ($notification->type == 'App\Notifications\VerificationRejectedLevel2')
                                                    <a href="">
                                                        <i class="fa fa-times-circle text-red"></i>
                                                        {{ $notification->data['action'] }}
                                                    </a>
                                                @endif

                                            </li>
                                        @endforeach
                                    </ul>
                                </li>

                            </ul>
                        </li>

                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                                <img src="{{ asset('dist/img/boxed-bg.jpg') }}" class="user-image" alt="User Image">
                                <span class="hidden-xs">
                                    {{ Auth::user()->firstname . ' ' . Auth::user()->lastname }}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header">
                                    <img src="{{ asset('dist/img/boxed-bg.jpg') }}" class="img-circle"
                                        alt="User Image">
                                    <p>
                                        {{ Auth::user()->firstname . ' ' . Auth::user()->lastname }}
                                    </p>

                                </li>
                                <!-- Menu Body -->

                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="{{ route('profile') }}">
                                            <button type="button" class="btn btn-primary">My Profile</button>

                                        </a>
                                    </div>
                                    <div class="pull-right">
                                        <a class="btn btn-danger btn-flat" href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                            Sign Out
                                        </a>
                                    </div>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                    </form>
                                </li>

                            </ul>
                        </li>
                        <!-- Control Sidebar Toggle Button -->

                    </ul>
                </div>

            </nav>
        </header>

        <!-- =============================================== -->

        @include('layouts.leftmenu')

        <!-- =============================================== -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    @yield('content-title')
                </h1>

            </section>

            <!-- Main content -->
            <section class="content">
                @yield('content')


            </section>
            <!-- /.content -->

        </div>
        <!-- /.content-wrapper -->

        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> 2.0
            </div>
            <strong>Copyright &copy; 2017-2018 <a target="_blank" rel="noopener noreferrer"
                    href="http://health.gov.ng/">FMOH</a>.</strong> All rights
            reserved.
        </footer>

    </div>
    <!-- ./wrapper -->


    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous">
    </script>
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('dist/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
    <script src=" https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>


    <script type="text/javascript" src="{{ asset('dist/multiselect-master/js/bootstrap-multiselect.js') }}"></script>


    <script>
        $(document).ready(function() {
            $('.sidebar-menu').tree()
        })

        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Date picker
            $('#datepicker').datepicker({
                autoclose: true,
                endDate: new Date(),
            })

        })
    </script>

    <script>
        $(document).ready(function() {
            // Success Alert 44
            @if (session('success'))
                Swal.fire({
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    // confirmButtonColor: '' // Optional: Customize button color
                    timer: 2000,

                    showConfirmButton: false
                });
            @endif

            // Error Alert
            @if (session('error'))
                Swal.fire({
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33' // Optional: Customize button color
                    timer: 2000,

                    showConfirmButton: false
                });
            @endif
        });
    </script>


    @yield('scripts')

    @stack('bk_script')

</body>

</html>
