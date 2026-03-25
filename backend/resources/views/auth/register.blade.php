@extends('layouts.master')

@section('content-title')
Create User

@endsection

@section('content')

<div class="panel panel-primary ">
        <div class="panel-heading" role="tab" id="headingOne">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        
                    </a>
                </h4>
        </div>
    <div class="panel-body">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel">
    
                <div class="panel-body">
                    <form method="POST" action="{{ route('reg') }}" aria-label="{{ __('Register') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="firstname" class="col-md-4 col-form-label text-md-right">{{ __('Fist Name') }}</label>

                            <div class="col-md-8">
                                <input id="firstname" type="text" class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}" name="firstname" value="{{ old('firstname') }}" required autofocus>

                                @if ($errors->has('firstname'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('firstname') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Last Name') }}</label>
    
                                <div class="col-md-8">
                                    <input id="lastname" type="text" class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}" name="lastname" value="{{ old('lastname') }}" required>
    
                                    @if ($errors->has('lastname'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('lastname') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('User Name') }}</label>
        
                                    <div class="col-md-8">
                                        <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" required>
        
                                        @if ($errors->has('username'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('username') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                            </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-8">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                                <label for="" class="col-md-4 col-form-label text-md-right">{{ __('User Role') }}</label>
                                <div class="col-md-8">
                                        <select class="form-control select2"  class="form-control{{ $errors->has('role') ? ' is-invalid' : '' }}" id="role" name="role" required>
                                                <option value="">--Select Role--</option>
                                                <option value="1">Admin</option>
                                        </select>
                                </div>
                                @if ($errors->has('role'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('role') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="form-group row">
                                <label for="" class="col-md-4 col-form-label text-md-right">{{ __('State') }}</label>
                                <div class="col-md-8">
                                        <select class="form-control select2" id="state" name ="state">
                                                <option value="">--Select State--</option>
                                                <option value='01' > Abia</option>
                                                <option value='02' > Adamawa</option>
                                                <option value='03' > Akwa Ibom</option>
                                                <option value='04' > Anambra</option>
                                                <option value='05' > Bauchi</option>
                                                <option value='06' > Bayelsa</option>
                                                <option value='07' > Benue</option>
                                                <option value='08' > Borno</option>
                                                <option value='09' > Cross River</option>
                                                <option value='10' > Delta</option>
                                                <option value='11' > Ebonyi</option>
                                                <option value='12' > Edo</option>
                                                <option value='13' > Ekiti</option>
                                                <option value='14' > Enugu</option>
                                                <option value='37' > FCT</option>
                                                <option value='15' > Gombe</option>
                                                <option value='16' > Imo</option>
                                                <option value='17' > Jigawa</option>
                                                <option value='18' > Kaduna</option>
                                                <option value='19' > Kano</option>
                                                <option value='20' > Katsina</option>
                                                <option value='21' > Kebbi</option>
                                                <option value='22' > Kogi</option>
                                                <option value='23' > Kwara</option>
                                                <option value='24' > Lagos</option>
                                                <option value='25' > Nasarawa</option>
                                                <option value='26' > Niger</option>
                                                <option value='27' > Ogun</option>
                                                <option value='28' > Ondo</option>
                                                <option value='29' > Osun</option>
                                                <option value='30' > Oyo</option>
                                                <option value='31' > Plateau</option>
                                                <option value='32' > Rivers</option>
                                                <option value='33' > Sokoto</option>
                                                <option value='34' > Taraba</option>
                                                <option value='35' > Yobe</option>
                                                <option value='36' > Zamfara</option>
                                            </select>
                                </div>
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-8">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-8">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                                <div class="col-md-4">
                                        <a href="/users">
                                            <button type="button" class="btn btn-danger">Cancel</button>
                                        </a>
                                </div>
                            <div class="col-md-8">
                                <button type="submit" class="btn btn-primary pull-right">
                                    {{ __('Register') }}
                                </button>
                            </div>
                          
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
