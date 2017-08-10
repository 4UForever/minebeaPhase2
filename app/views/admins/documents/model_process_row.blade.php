


<div class="row" data-id="{{$id}}">    
  <input type="hidden" name="document_indices[{{$id}}][id]" value="{{$id}}">                               
  <div class="col-xs-2">
    <div class="form-group">  
      <select class="chosen-select form-control document-line-select" name="document_indices[{{$id}}][line_id]" data-placeholder="Choose a line" data-current-select="{{$document_index['line_id']}}">    
        @foreach ($lines as $line)
          @if ($line->id == $document_index['line_id'])        
            <option value="{{$line->id}}" selected="selected">{{$line->title}}</option>
          @else
            <option value="{{$line->id}}">{{$line->title}}</option>
          @endif
        @endforeach
      </select>
    </div>   
  </div>
  <div class="col-xs-4 document-product-select"> 
    @include('admins.misc.ajax_loader', array('text' => 'Processing...'))
    <div class="form-group">  
      @foreach ($lines as $line) 
        @if ($line->id == $document_index['line_id'])  
          <select class="chosen-select form-control" name="document_indices[{{$id}}][product_id]" data-placeholder="Choose a product">    
            @foreach ($line->products as $product)
              @if ($product->id == $document_index['product_id'])        
                <option value="{{$product->id}}" selected="selected">{{$product->title}}</option>
              @else                                                          
                <option value="{{$product->id}}">{{$product->title}}</option>
              @endif
            @endforeach
          </select>
        @endif
      @endforeach     
    </div>  
  </div>
  <div class="col-xs-4 document-process-select">  
    @include('admins.misc.ajax_loader', array('text' => 'Processing...'))
    <div class="form-group">  
      @foreach ($lines as $line) 
        @if ($line->id == $document_index['line_id'])  
          <select class="chosen-select form-control" name="document_indices[{{$id}}][process_id]" data-placeholder="Choose a process">    
            @foreach ($line->processes as $process)
              @if ($process->id == $document_index['process_id'])        
                <option value="{{$process->id}}" selected="selected">{{$process->number}} - {{$process->title}}</option>
              @else
                <option value="{{$process->id}}">{{$process->number}} - {{$process->title}}</option>
              @endif
            @endforeach
          </select>
        @endif
      @endforeach     
    </div>                                                                                                     
  </div>
  <div class="col-xs-2">                     
    <div class="form-group">
      @if ($last_row)
        <button class="btn btn-default model-process-add-more">
          <span class="glyphicon glyphicon-plus"></span>
          <span class="glyphicon-class">Add more</span>
        </button>
        <button class="btn btn-warning model-process-remove">
          <span class="glyphicon glyphicon-remove"></span>
          <span class="glyphicon-class">Remove</span>
        </button>
      @else
        <button class="btn btn-warning model-process-remove model-process-remove-show">
          <span class="glyphicon glyphicon-remove"></span>
          <span class="glyphicon-class">Remove</span>
        </button>
      @endif
      @include('admins.misc.ajax_loader', array('text' => 'Please wait...'))
    </div>  
  </div>
</div>