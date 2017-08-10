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
      <span class="glyphicon-class">Import Excel file for production line {{$line->title}}</span>
    </h3>
  </div>
  <div class="row">                                                                   
    <form role="form" method="POST" enctype="multipart/form-data" action="{{url("admin/line/{$line->id}/import")}}">
      {{Form::token()}}
      <div class="col-xs-12">                                                                           
        <div class="form-group">              
          <label for="file">File</label>
          <input type="file" name="file" class="form-control" id="file">
        </div>                                                                                                                                                    
        <div class="form-group"> 
          <div class="text-center">                              
            <input type="submit" class="btn btn-success btn-submit" value="IMPORT">                                        
          </div>                                                            
        </div>                                                                      
      </div>                                                                      
    </form>
  </div>  
@stop   