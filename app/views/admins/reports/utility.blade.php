@extends('layouts.admin')

@section('css')
  @parent
  <link rel="stylesheet" href="{{asset('assets/datepicker/css/bootstrap-datepicker.min.css')}}">
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
      autoclose: true,
      startDate: "-30d"
    });
    $('.datepicker').change(function(){
      $.get("utility/ajax-stkpro", { type:'get-model', date: $(this).val() }, function(res){
        $('#model_id').html(res);
        $('#model_id').change();
      });
    });
    $('#model_id').change(function(){
      $.get("utility/ajax-stkpro", { type:'get-line', date: $("#date").val(), model_id:$(this).val() }, function(res){
        $('#line_id').html(res);
        $("#line_id").change();
      });
    });
    $('#line_id').change(function(){
      $.get("utility/ajax-stkpro", { type:'get-process', date: $("#date").val(), model_id:$("#model_id").val(), line_id:$(this).val() }, function(res){
        $('#process_id').html(res);
        $("#process_id").change();
      });
    });
    $('#process_id').change(function(){
      $.get("utility/ajax-stkpro", { type:'get-stkold', date: $("#date").val(), model_id:$("#model_id").val(), line_id:$("#line_id").val(), process_id:$(this).val() }, function(res){
        $('#stkpro-old').html(res);
      });
    });

    $("#btn-clear-continue").click(function(){
      $(this).attr("disabled", "disabled");
      $.get("utility/ajax-clear-continue", '', function(res){
        $('#tab1-result').html(res);
      });
    });
    $("#fm-reset-stkpro").submit(function(e){
      event.preventDefault(e);
      $(this).find("button[type='submit']").attr("disabled", "disabled");
      $('#tab2-result').html("ระบบกำลังคำนวณค่า Stock pro โปรดรอสักครู่...");
      $.get("utility/ajax-reset-stkpro", $(this).serialize(), function(res){
        if(res.error==1){
          $('#tab2-result').html('<font color="red">'+res.msg+'</font>');
        } else {
          $('#tab2-result').html(res.msg);
        }
        $(this).find("button[type='submit']").attr("disabled", "");
      });
    });
  });
  </script>
@stop

@section('content')

<style type="text/css">
.column_filter {
  width: 100%;
}
.result {
  border: 1px solid #ddd;
  padding: 10px;
  border-radius: 10px;
  margin: 5px 0;
}
</style>
<div class="page-header clearfix">
  <div class="row">
    <div class="col-xs-12">
      <h3>
        <span class="glyphicon glyphicon-folder-close"></span>
        <span class="glyphicon-class">
          Utility
        </span>
      </h3>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-12">
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#tab1">Clear continue log</a></li>
      <li><a data-toggle="tab" href="#tab2">Reset Stock PRO</a></li>
    </ul>
    <div class="tab-content">
      <div id="tab1" class="tab-pane in active">
        <p>
          <div class="row">
            <div class="col-md-12">
              <button type="button" class="btn btn-success" id="btn-clear-continue">คลิกเพื่อเริ่ม Clear continue log</button>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              ระบบจะทำการ Clear continue log ที่ค้างอยู่
            </div>
          </div>
        </p>
        <hr>
        <div class="result">
          <b>Result</b>
          <p id="tab1-result"></p>
        </div>
      </div>
      <div id="tab2" class="tab-pane">
        <p>
          <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="{{url("admin/utility/ajax-reset-stkpro")}}" id="fm-reset-stkpro">
            {{Form::token()}}
            <div class="form-group">
              <label class="col-sm-2 control-label">Select date :</label>
              <div class="col-sm-2">
                <input type="text" class="form-control datepicker" id="date" name="date" value="">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Select model :</label>
              <div class="col-sm-5">
                <select class="form-control" id="model_id" name="model_id">
                  <option value="">Choose model</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Select line :</label>
              <div class="col-sm-5">
                <select class="form-control" id="line_id" name="line_id">
                  <option value="">Choose line</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Select process :</label>
              <div class="col-sm-5">
                <select class="form-control" id="process_id" name="process_id">
                  <option value="">Choose process</option>
                </select>
              </div>
            </div>
            <div class="form-group form-inline">
              <label class="col-sm-2 control-label">Stock PRO :</label>
              <div class="col-sm-10">
                <label class="control-label">Current value :</label>
                <span class="form-control" id="stkpro-old"></span>
                <label class="control-label">Set new value :</label>
                <input type="text" class="form-control" id="stock_pro" name="stock_pro">
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-2">
                <button type="submit" class="btn btn-success btn-submit">Submit</button>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-12">
                *ระบบจะทำการบันทึกค่า Stock PRO เป็นค่าที่ท่านระบุ และคำนวณ stock pro ใหม่เริ่มจากวันที่และ Process ที่ท่านเลือกจนถึงวันปัจจุบัน
                <br>*สามารถ Reset ค่า Stock PRO ได้ไม่เกิน 1 เดือนย้อนหลัง (เนื่องจากระบบจะทำงานหนักตอนคำนวณค่าใหม่ทั้งหมด)
              </div>
            </div>
          </form>
        </p>
        <hr>
        <div class="result">
          <b>Result</b>
          <p id="tab2-result"></p>
        </div>
      </div>
    </div>
  </div>
</div>

@stop
