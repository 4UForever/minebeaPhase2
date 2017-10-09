@extends('layouts.admin')

@section('css')
  @parent
@stop

@section('js')
  @parent
  <script src="{{asset('assets/js/import-price.js')}}"></script>
@stop

@section('content')

<style type="text/css">
.column_filter {
  width: 100%;
}
</style>
<div class="page-header clearfix">
  <div class="row">
    <div class="col-xs-12">
      <h3>
        <span class="glyphicon glyphicon-folder-close"></span>
        <span class="glyphicon-class">
          Import Cycle time &amp; Unit price
        </span>
      </h3>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-12">
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#tab1">Data Cycle time &amp; Unit price</a></li>
      <li><a data-toggle="tab" href="#tab2">Import Cycle time &amp; Unit price</a></li>
    </ul>
    <div class="tab-content">
      <div id="tab1" class="tab-pane in active">
        <p>
          {{$datatable}}
        </p>
      </div>
      <div id="tab2" class="tab-pane">
        <p>
          <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="{{url("admin/import-price")}}">
            {{Form::token()}}
            <div class="form-group">
              <label class="col-sm-2 control-label">Select month :</label>
              <div class="col-sm-2">
                <select class="form-control" id="month" name="month">
                  <?php
                    $mon = array("01"=>"January","02"=>"Febuary","03"=>"March","04"=>"April","05"=>"May","06"=>"June","07"=>"July","08"=>"August","09"=>"September","10"=>"October","11"=>"November","12"=>"December");
                    for($i=1; $i<=12; $i++){
                      $selected = (date("m")==sprintf('%02d', $i))? " selected":"";
                      echo "<option value=\"".sprintf('%02d', $i)."\"".$selected.">".$mon[sprintf('%02d', $i)]."</option>";
                    }
                  ?>
                </select>
              </div>
              <label class="col-sm-1 control-label">Select year :</label>
              <div class="col-sm-2">
                <select class="form-control" id="year" name="year">
                  <?php
                    for($i=2017; $i<=date("Y")+1; $i++){
                      $selected = (date("Y")==$i)? " selected":"";
                      echo "<option value=\"".$i."\"".$selected.">".$i."</option>";
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Import file :</label>
              <div class="col-sm-10">
                <input type="file" name="file" class="form-control" id="file">
              </div>
            </div>
            <div class="form-group">
              <div class="text-center">
                <input type="submit" class="btn btn-success btn-submit" value="IMPORT">
              </div>
            </div>
          </form>
        </p>
      </div>
    </div>
  </div>
</div>

@stop
