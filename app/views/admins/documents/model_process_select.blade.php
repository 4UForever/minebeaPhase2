

      
@include('admins.misc.ajax_loader', array('text' => 'Processing...'))
<div class="form-group">
  <select class="chosen-select form-control" name="document_indices[{{$id}}][process_id]" data-placeholder="Choose a process">
    @foreach ($processes as $key => $process)
      @if ($key == 0)
        <option selected="selected" value="{{$process->id}}">{{$process->number}} - {{$process->title}}</option>
      @else
        <option value="{{$process->id}}">{{$process->number}} - {{$process->title}}</option> 
      @endif
    @endforeach
  </select>
</div>