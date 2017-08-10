@extends('layouts.admin')

@section('css') 
  @parent                                                                                                                                                                                        
@stop

@section('js')   
  @parent                                                     
@stop

@section('content') 
  <div class="page-header clearfix">
    <h3><span class="glyphicon glyphicon-wrench"></span>
      <span class="glyphicon-class">Are you sure you want to delete a process <i>{{$process->title}}?</i></span>
    </h3>
  </div>   
  <form role="form" method="POST" action="{{url("admin/process/{$process->id}/delete")}}">
    {{Form::token()}}
    <div class="col-md-7">                                                            
      <input type="submit" class="btn btn-danger" value="Delete"/>
      <a href='{{url("admin/process")}}' class="btn btn-warning">Cancel</a> 
    </div> 
  </form>                  
@stop  