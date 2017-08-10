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
    <h3><span class="glyphicon glyphicon-folder-close"></span>
      <span class="glyphicon-class">NEW DOCUMENT CATEGORY</span>
    </h3>
  </div>
  <div class="row">                                                                   
    <form enctype="multipart/form-data" role="form" method="POST" action="{{url("admin/document-category/create")}}">
      {{Form::token()}}
      <div class="col-xs-12">                                                                            
        <div class="form-group">              
          <label for="title">Title</label>
          <input type="text" name="title" class="form-control" id="title" placeholder="Enter document category title here" value="{{Input::get('title', '')}}">
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