@include('admins.misc.ajax_loader', array('text' => 'Processing...'))
<label for="product_id">Model</label>
<select class="chosen-select form-control" name="product_id" data-placeholder="Choose a model">
@foreach ($products as $key => $product)
  @if ($product->id == $product_old)
    <option selected="selected" value="{{$product->id}}">{{$product->title}}</option>
  @else
    <option value="{{$product->id}}">{{$product->title}}</option>
  @endif
@endforeach
</select>