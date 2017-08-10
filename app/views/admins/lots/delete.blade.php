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
      <span class="glyphicon-class">Are you sure you want to delete Lot <i>{{$lot->number}}?</i></span>
    </h3>
  </div>
  <div class="col-md-7">
    <h3 class="text-danger"><strong>Warning : This action will also delete lot number and processes in <i>{{$lot->number}}</i>.
      <br />So please ensure your action!!!</strong></h3>
    <a href="{{url("admin/lot/{$lot->id}/delete-confirmed")}}" class="btn btn-danger">Delete</a>
    <a href='{{url("admin/lot")}}' class="btn btn-warning">Cancel</a>
  </div>
@stop
