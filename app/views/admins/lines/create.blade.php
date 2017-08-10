@extends('layouts.admin')

@section('css') 
  @parent
@stop

@section('js')   
  @parent                                                                                                                                                      
  <script src="{{asset('assets/js/line.js')}}"></script>                                                                                  
@stop

@section('content')   
  <div class="page-header clearfix">
    <h3><span class="glyphicon glyphicon-th"></span>
      <span class="glyphicon-class">NEW PRODUCTION LINE</span>
    </h3>
  </div>
  <div class="row">                                                                   
    <form role="form" method="POST" action="{{url("admin/line/create")}}">
      {{Form::token()}}
      <div class="col-xs-12">                                                                           
        <div class="form-group">              
          <label for="title">Title</label>
          <input type="text" name="title" class="form-control" id="title" value="{{Input::old('title', '')}}" placeholder="Enter a production line title here">
        </div>               
        <div class="form-group">              
          <label for="group">Models</label>   
          <select class="chosen-select form-control" multiple="multiple" name="product_ids[]" data-placeholder="Choose at least one model or create a new one">    
            @foreach ($products as $product)
              @if (in_array($product->id, Input::old('product_ids', array())))        
                <option value="{{$product->id}}" selected="selected">{{$product->title}}</option>
              @else
                <option value="{{$product->id}}">{{$product->title}}</option>
              @endif
            @endforeach
          </select>   
        </div>     
        <div class="form-group">                  
          <div class="page-header clearfix">        
            <a href="{{url('admin/model/create-form')}}" id="add-new-product" class="btn btn-info">
              <span class="glyphicon glyphicon-plus-icon"></span>
              <span class="glyphicon-class">New model</span>
            </a>  
          </div>
          {{$new_products_table}}  
        </div>                 
        <div class="form-group">                              
          <div class="page-header clearfix">          
            <a href="{{url('admin/process/create-form')}}" id="add-new-process" class="btn btn-info">
              <span class="glyphicon glyphicon-plus-icon"></span>
              <span class="glyphicon-class">New process</span>
            </a>  
          </div>
          {{$new_processes_table}}  
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