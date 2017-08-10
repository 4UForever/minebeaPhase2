@extends('layouts.admin')

@section('css')
  @parent
@stop

@section('js')
  @parent
  <script src="{{asset('assets/js/part.js')}}"></script>
@stop

@section('content')
  <div class="page-header clearfix">
    <h3>
      <span class="glyphicon glyphicon-folder-close"></span>
      <span class="glyphicon-class">UPDATE PART: <i>{{$part->title}}</i></span>
    </h3>
  </div>
  <div class="row">
    <form enctype="multipart/form-data" role="form" method="POST" action="{{url("admin/part/{$part->id}/update")}}">
      {{Form::token()}}
      <div class="col-xs-12">
        <div class="form-group">
          <label for="Number">Part number</label>
          <input type="text" name="number" class="form-control" id="number" placeholder="Enter part number here" value="{{$part->number}}">
        </div>
        <div class="form-group">
          <label for="name">Name</label>
          <input type="text" name="name" class="form-control" id="name" placeholder="Enter part name here" value="{{$part->name}}">
        </div>
        <div class="form-group">
          <label for="group">Model</label>
          <select class="chosen-select form-control" name="product_id" id="product_id" data-placeholder="Choose a product">
            @foreach ($products as $product)
              @if ($product->id == $part->product->id)
                <option value="{{$product->id}}" selected="selected">{{$product->title}}</option>
              @else
                <option value="{{$product->id}}">{{$product->title}}</option>
              @endif
            @endforeach
          </select>
        </div>
        <div class="form-group" id="process-choose">
          {{$process_select}}
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