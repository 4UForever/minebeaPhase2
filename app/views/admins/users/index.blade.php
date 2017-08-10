@extends('layouts.admin')

@section('css') 
  @parent
@stop

@section('js')   
  @parent                                                    
  <script src="{{asset('assets/js/user.js')}}"></script>                                                                                                 
@stop

@section('content')   
  
  <div class="page-header clearfix">
    <div class="row">
      <div class="col-xs-6">
        <h3>
          <span class="glyphicon glyphicon-user"></span>
          <span class="glyphicon-class">
            USER
          </span>
        </h3>
      </div>
      <div class="col-xs-6">
        <a href="{{url('admin/user/create')}}" class="btn btn-success pull-right">
          <span class="glyphicon glyphicon-plus-icon"></span>
          <span class="glyphicon-class">New user</span>
        </a>
      </div>
    </div>
  </div>    
               
{{$datatable}}
          
@stop   