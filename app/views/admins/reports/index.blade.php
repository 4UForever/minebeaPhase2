@extends('layouts.admin')

@section('css')
  @parent
@stop

@section('js')
  @parent
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
          Import data to report
        </span>
      </h3>
    </div>
  </div>
</div>
<div class="row detail">
  <div class="col-xs-12">
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#tab1">Import Target PC</a></li>
      <li><a data-toggle="tab" href="#tab2">Import Circle time</a></li>
      <li><a data-toggle="tab" href="#tab3">Import Unit price</a></li>
    </ul>
    <div class="tab-content">
      <div id="tab1" class="tab-pane in active">
        <p>
          <h3>Import target PC</h3>
          <form class="form-horizontal">
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
      <div id="tab2" class="tab-pane">
        <p>
          <h3>Import Circle time</h3>
          <form class="form-horizontal">
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
      <div id="tab3" class="tab-pane">
        <p>
          <h3>Import Unit price</h3>
          <form class="form-horizontal">
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
