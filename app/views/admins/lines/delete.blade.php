@extends('layouts.admin')

@section('css') 
  @parent                                                                                                                                                                                        
@stop

@section('js')   
  @parent                                                     
@stop

@section('content') 
  <div class="page-header clearfix">
    <h3><span class="glyphicon glyphicon-th"></span>
      <span class="glyphicon-class">Are you sure you want to delete a production line <i>{{$line->title}}?</i></span>
    </h3>
  </div>   
  <form role="form" method="POST" action="{{url("admin/line/{$line->id}/delete")}}">
    {{Form::token()}}
    <div class="col-md-7">                                                            
      <input type="submit" class="btn btn-danger" value="Delete"/>
      <a href='{{url("admin/line")}}' class="btn btn-warning">Cancel</a> 
    </div> 
  </form>                  
@stop  