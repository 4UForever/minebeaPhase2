@foreach ($process_array as $key => $item)
  @if (is_null($item['pivot']['qty']))
  	@if (is_null($item['pivot']['process_log_id']))
		<span class="label label-default" title="wait for process">{{$item['number']}}</span>
  	@else
		<span class="label label-primary" title="on processing">{{$item['number']}}</span>
  	@endif
  @else
	<span class="label label-success" title="process finished">{{$item['number']}}</span>
  @endif
@endforeach