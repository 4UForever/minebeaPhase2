@extends('layouts.admin')

@section('css')
  @parent
<link rel="stylesheet" href="{{asset('assets/datepicker/css/bootstrap-datepicker.min.css')}}">
<style>
.table-responsive {
  font-size: x-small;
}
.table-responsive > table > thead > tr > th {
  text-align: center;
}
</style>
@stop

@section('js')
  @parent
<script src="{{asset('assets/datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('assets/datepicker/locales/bootstrap-datepicker.th.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){
  $('.datepicker').datepicker({
    format: 'yyyy-mm-dd',
    language: 'th',
    autoclose: true
  });

  $('.datepicker').change(function(){
    $.get("report-ajax-select", { type:'get-model', date: $(this).val() }, function(res){
      $('#model_id').html(res);
      $('#model_id').change();
    });
  });
  $('#model_id').change(function(){
    $.get("report-ajax-select", { type:'get-line', date: $("#date").val(), model_id:$(this).val() }, function(res){
      $('#line_id').html(res);
    });
  });
});
</script>
@stop

@section('content')

<div class="page-header clearfix">
  <div class="row">
    <div class="col-xs-12">
      <h3>
        <span class="glyphicon glyphicon-folder-close"></span>
        <span class="glyphicon-class">
          Production Daily Report {{ !empty($model_id)? "(".$model_title.") ".$line_title." [".$date."]":"" }}
        </span>
      </h3>
    </div>
  </div>
</div>

<div class="row">
  <form class="form-horizontal" role="form" method="GET" enctype="multipart/form-data" action="{{url("admin/report-daily")}}">
    <div class="col-md-8">
      <div class="form-group row">
        <label class="col-sm-2 control-label">Select date :</label>
        <div class="col-sm-2">
          <input type="text" class="form-control datepicker" id="date" name="date" value="{{ $date }}">
        </div>
        <label class="col-sm-2 control-label">Select model :</label>
        <div class="col-sm-2">
          <select class="form-control" id="model_id" name="model_id">
            @foreach($models as $model)
              <option value="{{ $model->product_id }}"{{ ($model_id==$model->product_id)? ' selected':'' }}>{{ $model->product_title }}</option>
            @endforeach
          </select>
        </div>
        <label class="col-sm-2 control-label">Select line :</label>
        <div class="col-sm-2">
          <select class="form-control" id="line_id" name="line_id">
            @foreach($lines as $line)
              <option value="{{ $line->line_id }}"{{ ($line_id==$line->line_id)? ' selected':'' }}>{{ $line->line_title }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-success">View Report</button>
    </div>
    <!-- <div class="col-md-2">
      <button type="submit" class="btn btn-success">View Report</button>
    </div> -->
  </form>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th rowspan="2">Process name</th>
              <th rowspan="2">Cycle time</th>
              <th rowspan="2">Working day</th>
              <th rowspan="2">Target PC</th>
              <th rowspan="2">Input</th>
              <th rowspan="2">Output</th>
              <th rowspan="2">Yield NG(%)</th>
              <th rowspan="2">Yield Accum(%)</th>
              <th rowspan="2">BAL PC</th>
              <th rowspan="2">Stock PRO</th>
              <th rowspan="2">Stock PC<br></th>
              <th rowspan="2">BAL Stock</th>
              <th colspan="{{ $resShiftSize }}">Result</th>
              <th colspan="5">Total NG<br></th>
              <th rowspan="2">Price NG<br></th>
              <th rowspan="2">Remark</th>
              <th rowspan="2">Down time<br></th>
              <th rowspan="2">Unit price<br></th>
            </tr>
            <tr>
              @foreach($shiftsAll as $shift)
              <td>{{ $shift['label'] }}</td>
              <td>NG1</td>
              @endforeach
              <td>NG1</td>
              <td>NG2</td>
              <td>Setup</td>
              <td>D/T</td>
              <td>TTL</td>
            </tr>
          </thead>
          <tbody>
            @if($data)
            @foreach($data as $arr)
            <tr>
              <td>{{ $arr['process'][0] }}</td>
              <td rowspan="2">{{ $arr['process'][1] }}</td>
              @for ($i=2; $i<count($arr['process']);$i++)
              <td>{{ $arr['process'][$i] }}</td>
              @endfor
            </tr>
            <tr>
              <td>{{ $arr['accum'][0] }}</td>
              @for ($i=2; $i<count($arr['accum']);$i++)
              <td>{{ $arr['accum'][$i] }}</td>
              @endfor
            </tr>
            @endforeach
            <tr>
              <td>Total cycle time</td>
              <td>{{ $total['cycle_time'] }}</td>
              <td colspan="2">Man Power</td>
              <td>xx</td>
              <td>Productivity 100%</td>
              <td>xx</td>
              <td>Productivity 80%</td>
              <td>-</td>
              <td colspan="{{ ($resShiftSize+3) }}"></td>
              <td colspan="4">NG / Day</td>
              <td>{{ $total['day_ttl'] }}</td>
              <td>{{ $total['day_price'] }}</td>
              <td colspan="3"></td>
            </tr>
            <tr>
              <td colspan="{{ ($resShiftSize+12) }}"></td>
              <td colspan="4">Accum NG</td>
              <td>{{ $total['accum_ttl'] }}</td>
              <td>{{ $total['accum_price'] }}</td>
              <td colspan="3"></td>
            </tr>
            @endif
          </tbody>
        </table>
    </div>
  </div>
</div>

@stop
