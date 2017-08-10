@extends('layouts.admin')

@section('css') 
  @parent            
  <link href="{{asset('assets/css/chosen/chosen.min.css')}}" rel="stylesheet">
  <link href="{{asset('assets/css/chosen/chosen.bootstrap.css')}}" rel="stylesheet">
@stop

@section('js')   
  @parent             
  <script src="{{asset('assets/js/chosen.jquery.min.js')}}"></script>                                 
  <script src="{{asset('assets/js/admin.chosen.js')}}"></script>                                                  
@stop

@section('content')   
  <div class="page-header clearfix">
    <h3><span class="glyphicon glyphicon-tags"></span>
      <span class="glyphicon-class">EDIT MODEL: <i>{{$product->title}}</i></span>
    </h3>
  </div>
  <div class="row">                                                                   
    <form role="form" method="POST" action="{{url("admin/model/{$product->id}/update")}}">
      {{Form::token()}}
      <div class="col-xs-12">                                                                           
        <div class="form-group">              
          <label for="title">Title</label>
          <input type="text" name="title" class="form-control" id="title" value="{{$product->title}}">
        </div>    
        <div class="form-group">              
          <label for="group">Lines</label>   
          <select class="chosen-select form-control" multiple="multiple" name="line_ids[]" data-placeholder="Choose models">    
            @foreach ($lines as $line)
              @if (in_array($line->id, $product_line_ids))        
                <option value="{{$line->id}}" selected="selected">{{$line->title}}</option>
              @else
                <option value="{{$line->id}}">{{$line->title}}</option>
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