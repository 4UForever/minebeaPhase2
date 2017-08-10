@extends('layouts.admin')

@section('css')
  @parent
@stop

@section('js')
  @parent
  <script src="{{asset('assets/js/lot.js')}}"></script>
@stop

@section('content')
  <div class="page-header clearfix">
    <h3>
      <span class="glyphicon glyphicon-folder-close"></span>
      <span class="glyphicon-class">Detail of Lot: <i>{{$lot->number}}</i></span>
    </h3>
  </div>
  <div class="row">
      {{Form::token()}}
      <div class="col-xs-12">
        <div class="form-group">
          <label for="group">Lot</label>
          <p class="form-control-static">{{$lot->number}}</p>
        </div>
        <div class="form-group" id="product-choose">
          <label for="group">Quantity</label>
          <p class="form-control-static">{{$lot->quantity}}</p>
        </div>
        <div class="form-group" id="product-choose">
          <label for="group">WIP</label>
          <p class="form-control-static">{{$lot->wip->title}}</p>
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
                <th>OK (Qty)</th>
                <th>Log ID</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($lot->processes as $key => $process)
              <tr>
                <td>{{$process->id}}</td>
                <td>{{$process->number}}</td>
                <td>{{$process->title}}</td>
                <td>{{$process->pivot->sort}}</td>
                <td>{{$process->pivot->qty}}</td>
                <td>
                  @if (!empty($process['pivot']['process_log_id']))
                    <?php if(!empty($process->pivot->qty)) $class = "success"; else $class = "default"; ?>
                    <a href="{{url("admin/process-log/{$process->pivot->process_log_id}/detail")}}" class="btn btn-{{$class}}">View process log ({{$process->pivot->process_log_id}})</a>
                  @else
                    {{$process->pivot->process_log_id}}
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          </div>
        </div>
        <div class="form-group">
          <div class="text-center">
            <a href="{{url("admin/lot")}}" class="btn btn-default">Back to Lots list</a>
          </div>
        </div>
      </div>
  </div>
@stop