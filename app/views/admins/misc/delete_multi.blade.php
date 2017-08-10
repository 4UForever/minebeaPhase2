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
      <span class="glyphicon-class">Are you sure you want to delete {{$delete['title']}} ID : <i>{{$delete['rows']}}?</i></span><br>
    </h3>
  </div>
  <div class="col-md-7">
    @if(!empty($delete['warning']))
      <h3 class="text-danger"><b>Warning !</b> {{$delete['warning']}}</h3>
    @endif
    <a href="{{$delete['url']}}/delete-multi-confirmed?rows={{$delete['rows']}}" class="btn btn-danger">Delete</a>
    <a href='{{$delete['url']}}' class="btn btn-warning">Cancel</a>
  </div>
@stop