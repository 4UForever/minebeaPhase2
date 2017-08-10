@extends('layouts.admin')

@section('css')
  @parent
@stop

@section('js')
  @parent
  <script src="{{asset('assets/js/process-working.js')}}"></script>
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
          Process woking
        </span>
      </h3>
    </div>
  </div>
</div>

{{$datatable}}

@stop