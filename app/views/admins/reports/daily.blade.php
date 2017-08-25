@extends('layouts.admin')

@section('css')
  @parent
@stop

@section('js')
  @parent
@stop

@section('content')

<div class="page-header clearfix">
  <div class="row">
    <div class="col-xs-12">
      <h3>
        <span class="glyphicon glyphicon-folder-close"></span>
        <span class="glyphicon-class">
          Daily report
        </span>
      </h3>
    </div>
  </div>
</div>
<div class="table-responsive">
  <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th width="5%">Process name</th>
          <th width="5%">Circle time</th>
          <th width="10%">Working day</th>
          <th width="10%">Target PC</th>
          <th width="10%">Input</th>
          <th width="10%">Output</th>
          <th width="10%">Yield NG</th>
          <th width="10%">Yield Accum</th>
          <th width="10%">BAL PC</th>
          <th width="10%">WIP</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>x</td>
          <td>x</td>
          <td>x</td>
          <td>x</td>
          <td>x</td>
          <td>x</td>
          <td>x</td>
          <td>x</td>
          <td>x</td>
          <td>x</td>
        </tr>
      </tbody>
    </table>
</div>

@stop
