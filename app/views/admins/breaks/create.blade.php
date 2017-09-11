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
    <h3><span class="glyphicon glyphicon-folder-close"></span>
      <span class="glyphicon-class">NEW Break reason</span>
    </h3>
  </div>
  <div class="row">
    <form enctype="multipart/form-data" role="form" method="POST" action="{{url("admin/break/create")}}">
      {{Form::token()}}
      <div class="col-xs-12">
        <div class="form-group">
          <label for="code">Code</label>
          <input type="text" name="code" class="form-control" id="code" placeholder="Enter Break reason code here" value="{{Input::get('code', '')}}">
        </div>
        <div class="form-group">
          <label for="reason">Title</label>
          <input type="text" name="reason" class="form-control" id="reason" placeholder="Enter Break reason title here" value="{{Input::get('reason', '')}}">
        </div>
        <div class="form-group">
          <label for="flag">Flag</label>
          <div class="form-control">
            <label class="radio-inline"><input type="radio" name="flag" value="1"{{(Input::get('flag', '')=="1")? " checked":"";}}> Yes</label>&nbsp;&nbsp;
            <label class="radio-inline"><input type="radio" name="flag" value="0"{{(Input::get('flag', '')=="0")? " checked":"";}}> No</label>
          </div>
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