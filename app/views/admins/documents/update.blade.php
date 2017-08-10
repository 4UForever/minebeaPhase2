@extends('layouts.admin')

@section('css') 
  @parent                                                                             
@stop

@section('js')   
  @parent                                                           
  <script src="{{asset('assets/js/document.js')}}"></script>                                                   
@stop

@section('content')   
  <div class="page-header clearfix">
    <h3><span class="glyphicon glyphicon-list-alt"></span>
      <span class="glyphicon-class">EDIT DOCUMENT: <i>{{$document->title}}</i></span>
      <a role="button" class="btn btn-default text-success pull-right" href="{{url("admin/document/{$document->id}/download")}}">
        <strong>Download</strong>
      </a>
    </h3>
  </div>
  <div class="row">                                                                   
    <form enctype="multipart/form-data" role="form" method="POST" action="{{url("admin/document/{$document->id}/update")}}">
      {{Form::token()}}
      <div class="col-xs-12">                                                                            
        <div class="form-group">                                                    
          <label for="upload_documents[]">Upload documents</label>   
          <input type="file" name="upload_documents" class="form-control" id="title">
        </div>         
        <div class="form-group">              
          <label for="document_category_id">Category</label>   
          <select class="chosen-select form-control" name="document_category_id" data-placeholder="Choose a category">    
            @foreach ($document_categories as $document_category)
              @if ($document_category->id == $document->document_category->id)        
                <option value="{{$document_category->id}}" selected="selected">{{$document_category->title}}</option>
              @else
                <option value="{{$document_category->id}}">{{$document_category->title}}</option>
              @endif
            @endforeach
          </select>
        </div>   
        <label>Lines, models, and processes</label> 
        <div class="model-and-process-form-group">
          <div class="row">
            <div class="col-xs-2">
              <p><strong>Lines</strong></p>
            </div>
            <div class="col-xs-4">
              <p><strong>Models</strong></p>
            </div>
            <div class="col-xs-4">
              <p><strong>Processes</strong></p>
            </div>
          </div>
          @foreach ($document_indices as $key => $document_index)  
            <?php 
              $temp = clone $lines;
              
              $last_row = $key == count($document_indices) - 1; 
              $id = $document_index['id'];
              
              foreach ($lines as $key => $line) {
                if ($line->id != $document_index['line_id'] && in_array($line->id, $selected_line_ids)) {
                  unset($lines[$key]);
                }
              }
            ?>
            @include('admins.documents.model_process_row', compact('last_row', 'document_index', 'lines', 'id'))
            <?php $lines = $temp; ?>
          @endforeach
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