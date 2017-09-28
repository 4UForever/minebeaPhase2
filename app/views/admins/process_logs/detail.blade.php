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
.detail li {
  padding: 2px 0;
}
.sp-key {
  display: inline-block;
  width: 30%;
}
.sp-val {
  display: inline-block;
  width: 200px;
  border: 1px solid #ccc;
  text-align: center;
}
</style>
  <div class="page-header clearfix">
    <h3>
      <span class="glyphicon glyphicon-folder-close"></span>
      <span class="glyphicon-class">Detail of process log ID : <i>{{$process_log->id}}</i></span>
    </h3>
  </div>
  <div class="row detail">
    <div class="col-sm-12">
      <div class="row">
        <label class="col-sm-3">ID</label>
        <p class="col-sm-9">{{$process_log->id}}</p>
      </div>
      <div class="row">
        <label class="col-sm-3">User</label>
        <p class="col-sm-9">{{$process_log->full_name}}</p>
      </div>
      <div class="row">
        <label class="col-sm-3">Working date</label>
        <p class="col-sm-9">{{$process_log->working_date}}</p>
      </div>
      <div class="row">
        <label class="col-sm-3">Shift</label>
        <p class="col-sm-9">{{$process_log->shift_label}} {{$process_log->shift_time}}</p>
      </div>
      <div class="row">
        <label class="col-sm-3">Line</label>
        <p class="col-sm-9">{{$process_log->line_title}}</p>
      </div>
      <div class="row">
        <label class="col-sm-3">Process</label>
        <p class="col-sm-9">{{$process_log->process_title}} ({{$process_log->process_number}})</p>
      </div>
      <div class="row">
        <label class="col-sm-3">Model</label>
        <p class="col-sm-9">{{$process_log->product_title}}</p>
      </div>
      <div class="row">
        <label class="col-sm-3">Lot number</label>
        <p class="col-sm-9">{{$process_log->lot_number}}</p>
      </div>
      <div class="row">
        <label class="col-sm-3">Line leader</label>
        <p class="col-sm-9">{{$process_log->line_leader_name}}</p>
      </div>
      <div class="row">
        <label class="col-sm-3">Log time</label>
        <p class="col-sm-9"><span class="bg-success" style="border: 1px solid #ccc;padding: 3px;">{{$process_log->start_time}}</span> - <span class="bg-danger" style="border: 1px solid #ccc;padding: 3px;">{{$process_log->end_time}}</span></p>
      </div>

      @foreach ($process_log_part as $key=>$part)
      <div class="row">
        <label class="col-sm-3">Part</label>
        <div class="col-sm-9">
            <ul class="list-unstyled">
              <li>{{$part->part_number}} ({{$part->part_name}})</li>
              <li>
                <ul class="list-inline">
                  <li><strong>IQC Lot :</strong></li>
                  @foreach ($process_log_input['IQC'] as $iqc)
                    @if ($iqc->part_id == $part->part_id)
                    <li><span style="border: 1px solid #ccc; padding:2px;">{{$iqc->lot_number}} (Qty:{{$iqc->use_qty}})</span></li>
                    @endif
                  @endforeach
                </ul>
              </li>
            </ul>
        </div>
      </div>
      @endforeach

      <div class="row">
        <label class="col-sm-3">WIP</label>
        <div class="col-sm-9">
          <ul class="list-inline">
            @foreach ($process_log_input['WIP'] as $wip)
              <li><span style="border: 1px solid #ccc; padding:2px;">{{$wip->lot_number}} (Qty:{{$wip->use_qty}})</span></li>
            @endforeach
          </ul>
        </div>
      </div>

      <div class="row">
        <label class="col-sm-3">OK</label>
        <div class="col-sm-9">{{number_format($process_log->ok_qty)}}</div>
      </div>

      <?php $total_ng = 0; ?>
      <div class="row">
        <label class="col-sm-3">NG</label>
        <div class="col-sm-9">
          <ul class="list-unstyled">
            @foreach ($process_log_ng as $key=>$ng)
            <?php $total_ng += $ng->quantity; ?>
              <li><span class="sp-key">{{$ng->ng_title}}</span><span class="sp-val">{{$ng->quantity}}</span></li>
            @endforeach
            <li><span class="sp-key"><strong>Total NG :</strong></span><span class="sp-val">{{$total_ng}}</span></li>
          </ul>
        </div>
      </div>

      <?php $total_break = 0; ?>
      <div class="row">
        <label class="col-sm-3">Break time</label>
        <div class="col-sm-9">
          <ul class="list-unstyled">
            @foreach ($processes_log_break as $key=>$break)
            <?php $total_break += $break->total_minute; ?>
              <li><span class="sp-key">{{$break->break_reason}} ({{$break->break_code}})</span><span class="sp-val">{{$break->start_break}}</span> - <span class="sp-val">{{$break->end_break}}</span></li>
            @endforeach
            <li><span class="sp-key"><strong>Total break :</strong></span><span class="sp-val">{{$total_break}}</span> Minutes</li>
          </ul>
        </div>
      </div>

      <div class="row">
        <label class="col-sm-3">First serial number</label>
        <p class="col-sm-9">{{$process_log->first_serial_no}}</p>
      </div>
      <div class="row">
        <label class="col-sm-3">Last serial number</label>
        <p class="col-sm-9">{{$process_log->last_serial_no}}</p>
      </div>
      <div class="row">
        <label class="col-sm-3">Setup</label>
        <p class="col-sm-9">{{$process_log->setup}}</p>
      </div>
      <div class="row">
        <label class="col-sm-3">D/T</label>
        <p class="col-sm-9">{{$process_log->dt}}</p>
      </div>
      <div class="row">
        <label class="col-sm-3">Wip Quantity</label>
        <p class="col-sm-9">{{$process_log->wip_qty}}</p>
      </div>
      <div class="row">
        <label class="col-sm-3">Running time</label>
        <p class="col-sm-9">{{number_format(($process_log->total_minute - $total_break))}} Minutes</p>
      </div>

      <div class="form-group">
        <div class="text-center">
          <button type="button" class="btn btn-success btn-submit" onclick="location.href='/admin/process-log';">OK</button>
        </div>
      </div>
    </div>
  </div>
@stop