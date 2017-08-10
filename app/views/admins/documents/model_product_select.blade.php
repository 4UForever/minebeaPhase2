

      
@include('admins.misc.ajax_loader', array('text' => 'Processing...'))
<div class="form-group">
  <select class="chosen-select form-control" name="document_indices[{{$id}}][product_id]" data-placeholder="Choose a product">
    @foreach ($products as $key => $product)
      @if ($key == 0)
        <option selected="selected" value="{{$product->id}}">{{$product->title}}</option>
      @else
        <option value="{{$product->id}}">{{$product->title}}</option> 
      @endif                                                       
    @endforeach
  </select>
</div>