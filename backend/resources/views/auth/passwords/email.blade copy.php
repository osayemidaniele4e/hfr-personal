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
                @if (session('status'))
                    <div class="alert alert-success" role="alert"> 
                        {{ session('status') }} <br>
                        <font color="black"> If you dont see email in your inbox, Please check your spam/junk folder!</font>
                    </div>
                   
                @endif
              
               <form method="POST" action="{{route('password.email') }}" aria-label="{{ __('Reset Password') }}">
                @csrf
                    
                    <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Enter your E-mail" required autofocus>
                        </div>
                        @if ($errors->has('email'))
                            <span class="help-block">
                                {{ $errors->first('email') }}
                            </span>
                         @endif
                    </div>
                           
                    <div class="form-group">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Send Password Reset Link') }}
                            </button>
                        </div>
                    </div>
    
              </form>
        </div>
       
        
        
        
        
      </div>
      <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
-
    <script src="{{ asset("dist/js/jquery.min.js")}}"></script>
    <script src="{{ asset("dist/js/bootstrap.min.js")}}"></script>
    <script>
      
    </script>
  </body>
  </html>
  