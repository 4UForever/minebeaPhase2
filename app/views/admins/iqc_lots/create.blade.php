@extends('layouts.admin')

@section('css')
  @parent
@stop

@section('js')
  @parent
  <script src="{{asset('assets/js/iqc_lot.js')}}"></script>
@stop

@section('content')
  <div class="page-header clearfix">
    <h3>
      <span class="glyphicon glyphicon-folder-close"></span>
      <span class="glyphicon-class">NEW IQC Lot</span>
    </h3>
  </div>
  <div class="row">
    <form enctype="multipart/form-data" role="form" method="POST" action="{{url("admin/iqc-lot/create")}}">
      {{Form::token()}}
      <div class="col-xs-12">
        <div class="form-group">
          <label for="Number">IQC Lot number</label>
          <input type="text" name="number" class="form-control" id="number" placeholder="Enter IQC Lot number here" value="{{Input::get('number', '')}}">
        </div>
        <div class="form-group">
          <label for="supplier_name">Supplier name</label>
          <input type="text" name="supplier_name" class="form-control" id="supplier_name" placeholder="Enter Supplier name here" value="{{Input::get('supplier_name', '')}}">
        </div>
        <div class="form-group">
          <label for="invoice_number">Invoice number</label>
          <input type="text" name="invoice_number" class="form-control" id="invoice_number" placeholder="Enter Invoice number here" value="{{Input::get('invoice_number', '')}}">
        </div>
        <div class="form-group">
          <label for="quantity">Quantity</label>
          <input type="text" name="quantity" class="form-control" id="quantity" placeholder="Enter Quantity here" value="{{Input::get('quantity', '')}}">
        </div>
        <div class="form-group">
          <label for="group">Part</label>
          <select class="chosen-select form-control" name="part_id" id="part_id" data-placeholder="Choose a part">
            @foreach ($parts as $part)
              @if ($part->id == Input::old('part_id'))
                <option value="{{$part->id}}" selected="selected">{{$part->number}} - ({{$part->name}})</option>
              @else
                <option value="{{$part->id}}">{{$part->number}} - ({{$part->name}})</option>
              @endif
            @endforeach
          </select>
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