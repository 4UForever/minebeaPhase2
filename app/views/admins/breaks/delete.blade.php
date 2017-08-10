@extends('layouts.admin')

@section('css')
  @parent
@stop

@section('js')
  @parent
@stop

@section('content')
  <div class="page-header clearfix">
    <h3><span class="glyphicon glyphicon-folder-close"></span>
      <span class="glyphicon-class">Are you sure you want to delete Break reason <i>{{$break->reason}}?</i></span>
    </h3>
  </div>
  <form role="form" method="POST" action="{{url("admin/break/{$break->id}/delete")}}">
    {{Form::token()}}
    <div class="col-md-7">
      <input type="submit" class="btn btn-danger" value="Delete"/>
      <a href='{{url("admin/break")}}' class="btn btn-warning">Cancel</a>
    </div>
  </form>
@stop