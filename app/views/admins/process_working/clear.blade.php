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
      <span class="glyphicon-class">Are you sure you want to clear process for user <i>{{$user->first_name.' '.$user->last_name}}?</i></span>
    </h3>
  </div>
  <div class="col-md-7">
    @if (empty($process_log->lot_id))
    <a href="{{url("admin/process-working/{$user->id}/normal-clear")}}" class="btn btn-danger">Clear</a>
    <a href='{{url("admin/process-working")}}' class="btn btn-warning">Cancel</a>
    @else
    <h3 class="text-danger"><strong>Warning : This process is a part of lot number <i>{{$process_log->lot_number}}</i>.
      <br />If you confirm to clear this process, this action will also delete lot number <i>{{$process_log->lot_number}}</i>. 
      <br />So please ensure your action!!!</strong></h3>
    <a href="{{url("admin/process-working/{$user->id}/force-clear")}}" class="btn btn-danger">Force clear</a>
    <a href='{{url("admin/process-working")}}' class="btn btn-warning">Cancel</a>
    @endif
  </div>
@stop