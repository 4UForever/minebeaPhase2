@extends('layouts.admin')

@section('css') 
  @parent
@stop

@section('js')   
  @parent         
  <script src="{{asset('assets/js/group.js')}}"></script>                                         
@stop

@section('content')   
  
<div class="page-header clearfix">
  <div class="row">
    <div class="col-xs-6">    
      <h3>
        <span class="glyphicon glyphicon-th"></span>
        <span class="glyphicon-class">
          USER GROUP
        </span>
      </h3>
    </div>
    <div class="col-xs-6">
      <div class="btn-group pull-right">             
        <a href="{{url("admin/group/create")}}" class="btn btn-success">
          <span class="glyphicon glyphicon-plus-icon"></span>
          <span class="glyphicon-class">New user group</span>
        </a>
        <a href="{{url("admin/permission")}}" type="button" class="btn btn-primary">
          <span class="glyphicon glyphicon-ok-circle"></span>
          <span class="glyphicon-class">Permissions</span>
        </a> 
      </div> 
    </div>
  </div>   
</div>      
               
{{$datatable}}
          
@stop   