@extends('layouts.admin')

@section('css')
  @parent
@stop

@section('js')
  @parent
  <script src="{{asset('assets/js/wip.js')}}"></script>
@stop

@section('content')

<div class="page-header clearfix">
  <div class="row">
    <div class="col-xs-6">
      <h3>
        <span class="glyphicon glyphicon-folder-close"></span>
        <span class="glyphicon-class">
          Condition (WIP)
        </span>
      </h3>
    </div>
    <div class="col-xs-6 text-right">
      <!--<a href="{{url("admin/wip/import")}}" class="btn btn-success"><span class="glyphicon glyphicon-import"></span> <span class="glyphicon-class">Import Excel</span></a>
      <a href="{{url("admin/wip/export")}}" class="btn btn-success"><span class="glyphicon glyphicon-export"></span> <span class="glyphicon-class">Export Excel</span></a>-->
      <a href="{{url("admin/wip/create")}}" class="btn btn-success"><span class="glyphicon glyphicon-plus-icon"></span><span class="glyphicon-class">NEW WIP</span></a>
    </div>
  </div>
</div>

{{$datatable}}

@stop