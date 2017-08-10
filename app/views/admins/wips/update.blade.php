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
      <span class="glyphicon-class">UPDATE Condition (WIP): <i>{{$wip->title}}</i></span>
    </h3>
  </div>
  <div class="row">
    <form enctype="multipart/form-data" role="form" method="POST" action="{{url("admin/wip/{$wip->id}/update")}}">
      {{Form::token()}}
      <div class="col-xs-12">
        <div class="form-group">
          <label for="title">Title</label>
          <input type="text" name="title" class="form-control" id="title" placeholder="Enter wip title here" value="{{$wip->title}}">
        </div>
        <div class="form-group">
          <label for="group">Line</label>
          <p class="form-control-static">{{$wip->line->title}}</p>
        </div>
        <div class="form-group" id="product-choose">
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
              </tr>
            </thead>
            <tbody>
              @foreach ($wip->processes as $key => $process)
              <tr>
                <td>{{$process->id}}</td>
                <td>{{$process->number}}</td>
                <td>{{$process->title}}</td>
                <td>{{$process->pivot->sort}}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
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