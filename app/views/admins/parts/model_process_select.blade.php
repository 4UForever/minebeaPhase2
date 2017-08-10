@include('admins.misc.ajax_loader', array('text' => 'Processing...'))
<label for="process_id">Process</label>
<select class="chosen-select form-control" name="process_id" data-placeholder="Choose a process">
@foreach ($processes as $key => $process)
  @if ($process->id == $process_old)
    <option selected="selected" value="{{$process->id}}">{{$process->number}} - {{$process->title}}</option>
  @else
    <option value="{{$process->id}}">{{$process->number}} - {{$process->title}}</option>
  @endif
@endforeach
</select>