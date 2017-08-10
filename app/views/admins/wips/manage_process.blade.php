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
    <h3>
      <span class="glyphicon glyphicon-folder-close"></span>
      <span class="glyphicon-class">Process of Condition (WIP): <i>{{$wip->title}}</i></span>
    </h3>
  </div>
  <div class="row">
    <form enctype="multipart/form-data" role="form" method="POST" action="{{url("admin/wip/{$wip->id}/processes")}}">
      {{Form::token()}}
      <div class="col-xs-12">
        <div class="form-group">
          <label for="title">Title</label>
          <p class="form-control-static">{{$wip->title}}</p>
        </div>
        <div class="form-group">
          <label for="group">Line</label>
          <p class="form-control-static">{{$wip->line->title}}</p>
        </div>
        <div class="form-group">
          <label for="group">Model</label>
          <p class="form-control-static">{{$wip->product->title}}</p>
        </div>
        <div class="form-group">
          <label for="group">Process list</label>
          <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Process (Number)</th>
                <th>Process title</th>
                <th>Order</th>
                <th>Delete</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($wip->processes as $key => $process)
              <tr>
                <td>{{$process->id}}</td>
                <td>{{$process->number}}</td>
                <td>{{$process->title}}</td>
                <td>{{$process->pivot->sort}}</td>
                <td><a href="{{url("admin/wip/$wip->id/processes-detach/$process->id")}}" class="btn btn-danger" onclick="return confirm('Are you sure want to delete this process?');">Delete</a></td>
              </tr>
              @endforeach
            </tbody>
          </table>
          </div>
        </div>
        <div class="form-group">
          <label for="process_id">Process</label>
          <select class="chosen-select form-control" name="process_id" data-placeholder="Choose a process">
          @foreach ($processes as $key => $process)
            @if ($process->id == $process_old)
              <option selected="selected" value="{{$process->id}}">{{$process->number}} - {{$process->title}}</option>
            @else
              <option value="{{$process->id}}">{{$process->number}} - {{$process->title}}</option>
            @endif
          @endforeach
          </select>
        </div>
        <div class="form-group">
          <div class="text-center">
            <input type="submit" class="btn btn-success btn-submit" value="Add process">
          </div>
        </div>
        <div class="form-group">
          <div class="text-center">
            <a href="{{url("admin/wip")}}" class="btn btn-default">Back to WIP list</a>
          </div>
        </div>
      </div>
    </form>
  </div>
@stop
