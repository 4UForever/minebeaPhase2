@extends('layouts.admin')

@section('css')
  @parent
@stop

@section('js')
  @parent
  <script src="{{asset('assets/js/break.js')}}"></script>
@stop

@section('content')
  <div class="page-header clearfix">
    <h3>
      <span class="glyphicon glyphicon-folder-close"></span>
      <span class="glyphicon-class">UPDATE Break reason: <i>{{$break->title}}</i></span>
    </h3>
  </div>
  <div class="row">
    <form enctype="multipart/form-data" role="form" method="POST" action="{{url("admin/break/{$break->id}/update")}}">
      {{Form::token()}}
      <div class="col-xs-12">
        <div class="form-group">
          <label for="code">Code</label>
          <input type="text" name="code" class="form-control" id="code" placeholder="Enter Break reason code here" value="{{$break->code}}">
        </div>
        <div class="form-group">
          <label for="reason">Title</label>
          <input type="text" name="reason" class="form-control" id="reason" placeholder="Enter Break reason title here" value="{{$break->reason}}">
        </div>
        <div class="form-group">
          <div class="text-center">
            <input type="submit" class="btn btn-success btn-submit" value="SUBMIT">
          </div>
        </div>
      </div>
    </form>
  </div>
@stop