@extends('layouts.admin')

@section('css')
  @parent
@stop

@section('js')
  @parent
  <script src="{{asset('assets/js/process-log.js')}}"></script>
@stop

@section('content')

<style type="text/css">
.column_filter {
  width: 100%;
}
</style>
<div class="page-header clearfix">
  <div class="row">
    <div class="col-xs-6">
      <h3>
        <span class="glyphicon glyphicon-folder-close"></span>
        <span class="glyphicon-class">
          Process log
        </span>
      </h3>
    </div>
    <div class="col-xs-6 text-right">
      <p><a href="{{url("admin/process-log/export")}}" class="btn btn-success"><span class="glyphicon glyphicon-export"></span> <span class="glyphicon-class">Export Excel (All)</span></a></p>
      <p>
        <label>Status filter</label>
        <select id="complete_filter">
          <option value="1">All</option>
          <option value="2">Only complete logs</option>
          <option value="3">Only incomplete logs</option>
        </select>
      </p>
    </div>
  </div>
</div>

{{$datatable}}

<div class="page-footer">
  <b>Export selected records</b><br>
  <button class="btn btn-success export-selected" data-role="selected"><span class="glyphicon glyphicon-export"></span> <span class="glyphicon-class">Export process log</span></button>
  <button class="btn btn-success export-selected" data-role="break"><span class="glyphicon glyphicon-export"></span> <span class="glyphicon-class">Export break</span></button>
  <button class="btn btn-success export-selected" data-role="ng"><span class="glyphicon glyphicon-export"></span> <span class="glyphicon-class">Export NG</span></button>
  <button class="btn btn-success export-selected" data-role="input"><span class="glyphicon glyphicon-export"></span> <span class="glyphicon-class">Export input</span></button>
</div>

@stop