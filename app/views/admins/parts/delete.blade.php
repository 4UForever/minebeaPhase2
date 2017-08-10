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
      <span class="glyphicon-class">Are you sure you want to delete a part <i>{{$part->title}}?</i></span><br>
    </h3>
  </div>
  <form role="form" method="POST" action="{{url("admin/part/{$part->id}/delete")}}">
    {{Form::token()}}
    <div class="col-md-7">
      <h3 class="text-danger"><b>Warning !</b> Any IQC Lots in this part will be delete.</h3>
      <input type="submit" class="btn btn-danger" value="Delete"/>
      <a href='{{url("admin/part")}}' class="btn btn-warning">Cancel</a>
    </div>
  </form>
@stop