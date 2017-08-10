@extends('layouts.login')

@section('css') 
  @parent
@stop

@section('js')   
  @parent                                                 
@stop
                   
@section('content')
  <div class="container content user-container">  
    <div class="signin-head">
      <div class="row">
        <div class="col-xs-6"><img src="{{asset('assets/images/tt-signin.png')}}" alt=""></div>
        <div class="col-xs-6"><img src="{{asset('assets/images/signin-logo.png')}}" alt=""></div>
      </div>
    </div>   
    
    @if (Session::has('success'))
      <p>
        <div class="alert alert-success">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <p>{{Session::get('success')}}</p>
        </div>
      </p>
    @endif 
    @if ($errors->any())
      <p>
        <div class="alert alert-danger">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          @foreach ($errors->all() as $error)
            <p>{{$error}}</p>
          @endforeach
        </div>
      </p>
    @endif    
                                                                                   
    <form role="form" method="POST" action="{{url('admin/user/login')}}">
      {{Form::token()}} 
      <div class="form-group has-feedback">                                                
        <input type="email" name="email" class="form-control" id="email" placeholder="Email">
        <span class="glyphicon glyphicon-username-icon form-control-feedback"></span>
      </div> 
      <div class="form-group has-feedback">
        <input type="password" name="password" class="form-control" id="password" placeholder="Password">
        <span class="glyphicon glyphicon-password-icon form-control-feedback"></span>
      </div>      
      <button type="submit" class="btn btn-default sign-in-button">SIGN IN</button>
    </form>   
  </div>
@stop