<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Nigeria Health Facility Registry</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="{{ asset("dist/css/bootstrap.min.css")}}" >
  <link rel="stylesheet" href="{{ asset("dist/css/font-awesome/css/font-awesome.min.css")}}" >
  <link rel="stylesheet" href="{{ asset("dist/css/ionicons/css/ionicons.min.css")}}">
  <link rel="stylesheet" href="{{ asset("dist/css/AdminLTE.min.css")}}">

  
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  </head>
  <body class="hold-transition login-page">
    <div class="login-box">
      <div>
            <a href="{{route('home')}}"> 
                <img class="img-responsive center-block" src="{{asset('img/logo_fmoh.png')}}" width="80" height="80" title="Nigeria Health Facility Registry" />
            </a>
      </div>
      <div class="login-logo">
            <a href="{{route('home')}}"> 
                    <span class="text-center"><h3>Nigeria Health Facility Registry</h3></span>
            </a>
        
      </div>
      <!-- /.login-logo -->
      <div class="panel">
        

        <div class="panel-body">
               <form method="POST" action="{{route('login.token') }}" >
                @csrf

                <div>
                    <p class="login-box-msg"><strong>Token Verification</strong></p>
                </div>
                <div class="form-group {{ $errors->has('token') ? 'has-error' : '' }}">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-puzzle-piece"></i></span>
                        <input id="token" type="text" class="form-control" name="token" value="{{ old('token') }}" placeholder="Enter Token" required autofocus>
                    </div>
                    @if ($errors->has('token'))
                        <span class="help-block">
                            {{ $errors->first('token') }}
                        </span>
                     @endif
                </div>
                         
            
                <div class="row">
                 
                  <!-- /.col -->
                  <div class="col-xs-8 pull-right">
                    <button type="submit" class="btn btn-success btn-block">Verify Token</button>
                  </div>
                  <!-- /.col -->
                </div>

              </form>
        </div>
       
        
        
        
        
      </div>
      <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->

    <script src="{{ asset("dist/js/jquery.min.js")}}"></script>
    <script src="{{ asset("dist/js/bootstrap.min.js")}}"></script>
    {{-- <script src='https://www.google.com/recaptcha/api.js'></script> --}}
  </body>
  </html>
  