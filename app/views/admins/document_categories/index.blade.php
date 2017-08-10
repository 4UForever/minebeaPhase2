@extends('layouts.admin')

@section('css')    
  @parent                                                                               
@stop

@section('js')   
  @parent                                                                             
  <script src="{{asset('assets/js/document_category.js')}}"></script>                
@stop

@section('content')   
  
<div class="page-header clearfix">
  <div class="row">
    <div class="col-xs-6">    
      <h3>
        <span class="glyphicon glyphicon-folder-close"></span>
        <span class="glyphicon-class">
          Document category
        </span>
      </h3>
    </div>
    <div class="col-xs-6">                                             
      <a href="{{url("admin/document-category/create")}}" class="btn btn-success pull-right">
        <span class="glyphicon glyphicon-plus-icon"></span>
        <span class="glyphicon-class">New document category</span>
      </a>
    </div>                  
  </div>   
</div>         
               
{{$datatable}}
          
@stop   