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
      <span class="glyphicon-class">NEW Condition (WIP)</span>
    </h3>
  </div>
  <div class="row">
    <form enctype="multipart/form-data" role="form" method="POST" action="{{url("admin/wip/create")}}">
      {{Form::token()}}
      <div class="col-xs-12">
        <div class="form-group">
          <label for="title">Title</label>
          <input type="text" name="title" class="form-control" id="title" placeholder="Enter wip title here" value="{{Input::get('title', '')}}">
        </div>
        <div class="form-group">
          <label for="group">Line</label>
          <select class="chosen-select form-control" name="line_id" id="line_id" data-placeholder="Choose a line">
            @foreach ($lines as $line)
              @if ($line->id == Input::old('line_id'))
                <option value="{{$line->id}}" selected="selected">{{$line->title}}</option>
              @else
                <option value="{{$line->id}}">{{$line->title}}</option>
              @endif
            @endforeach
          </select>
        </div>
        <div class="form-group" id="product-choose">
          {{$product_select}}
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