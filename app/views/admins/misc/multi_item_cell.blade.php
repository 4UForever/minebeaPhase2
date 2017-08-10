@foreach ($items as $key => $item)
  @if ($key % $col_num == 0 && $key != 0)
    <!--<br>-->
  @endif
  <span class="badge">{{$item}}</span>
@endforeach
