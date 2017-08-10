@extends('layouts.admin')

@section('css') 
  @parent                                                                                                                                                                                        
@stop

@section('js')   
  @parent                                                     
@stop

@section('content') 
  <div class="page-header clearfix">
    <h3>Delete User: <i>{{$user->email}}</i></h3>
  </div>   
  <h4>Are you sure you want to delete user <i>{{$user->email}}?</i></h4>
  <form role="form" method="POST" action="{{url("admin/user/{$user->id}/delete")}}">
    {{Form::token()}}
    <input type="submit" class="btn btn-danger" value="Delete"/>
    <a href='{{url('admin/user')}}' class="btn btn-warning">Cancel</a> 
  </form>                  
@stop  