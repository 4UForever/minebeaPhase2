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
    <h3><span class="glyphicon glyphicon-folder-close"></span>
      <span class="glyphicon-class">NEW NG Detail</span>
    </h3>
  </div>
  <div class="row">
    <form enctype="multipart/form-data" role="form" method="POST" action="{{url("admin/ng-detail/create")}}">
      {{Form::token()}}
      <div class="col-xs-12">
        <div class="form-group">
          <label for="group">Process</label>
          <select class="chosen-select form-control" name="process_id" id="process_id" data-placeholder="Choose a process">
            @foreach ($processes as $process)
              @if ($process->id == $process_old)
                <option value="{{$process->id}}" selected="selected">({{$process->number}}) {{$process->title}}</option>
              @else
                <option value="{{$process->id}}">({{$process->number}}) {{$process->title}}</option>
              @endif
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label for="Title">Title</label>
          <input type="text" name="title" class="form-control" id="title" placeholder="Enter NG Detail title here" value="{{Input::get('title', '')}}">
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