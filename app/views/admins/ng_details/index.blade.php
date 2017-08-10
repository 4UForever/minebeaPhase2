@extends('layouts.admin')

@section('css')
  @parent
@stop

@section('js')
  @parent
  <script src="{{asset('assets/js/ng-detail.js')}}"></script>
@stop

@section('content')

<div class="page-header clearfix">
  <div class="row">
    <div class="col-xs-6">
      <h3>
        <span class="glyphicon glyphicon-folder-close"></span>
        <span class="glyphicon-class">
          NG Detail
        </span>
      </h3>
    </div>
    <div class="col-xs-6 text-right">
      <a href="{{url("admin/ng-detail/import")}}" class="btn btn-success"><span class="glyphicon glyphicon-import"></span> <span class="glyphicon-class">Import Excel</span></a>
      <a href="{{url("admin/ng-detail/export")}}" class="btn btn-success"><span class="glyphicon glyphicon-export"></span> <span class="glyphicon-class">Export Excel</span></a>
      <a href="{{url("admin/ng-detail/create")}}" class="btn btn-success"><span class="glyphicon glyphicon-plus-icon"></span><span class="glyphicon-class">NEW NG Detail</span></a>
    </div>
  </div>
</div>

{{$datatable}}

<div class="page-footer">
  <button class="btn btn-danger btn-delete"><span class="glyphicon glyphicon-trash"></span> Delete selected</button>
</div>

@stop