@extends('layouts.pub.master')



@section('content')
<div class="latest-area section-padding bg-white">
    <div class="container">
        <div class="row">

            <form method="POST" action="{{route('validateToken')}}" class="form-horizontal">
                               @csrf

                                <div class="form-group row">
                                    <label for="token" class="col-md-3 control-label"></label>                                    
                                    <div class="col-md-6">
                                            <div role="alert" class="alert alert-success"> 
                                                    Please enter verification code sent in your email. <font color="red"> If you dont see email in your inbox, Please check your spam/junk folder!</font>
                                            </div> 
                                    </div>
                                   
                                </div>
                              
                                <div class="form-group row {{ $errors->has('token') ? 'has-error' : '' }}">
                                        <label for="token" class="col-md-3 control-label"></label>                                    

                                        <div class="col-md-6">
                                            <input id="token" type="text" class="form-control" name="token" value="{{ old('token') }}" placeholder="Enter Token" required autofocus>
                                            
                                            @if ($errors->has('token'))
                                                <span class="help-block">
                                                    {{ $errors->first('token') }}
                                                </span>
                                            @endif
                                        </div>

                                     
                                </div>

                           
                    
                                <div class="form-group row">
                                    <div class="col-md-3"> </div>
                                    
                                    <div class="col-md-6">
                                            <button type="submit" class="btn btn-success pull-right">Validate</button>
                                    </div>
                                    
                                </div>
                                
                                
                            </form>
                    
                
            
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom_scripts')
 


@endpush