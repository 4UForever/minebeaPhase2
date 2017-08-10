



<div class="btn-group">
  @foreach ($buttons as $button)
    <a href='{{$button['url']}}' type='button' class='btn btn-{{$button['type']}}'>{{$button['text']}}</a>
  @endforeach
</div>