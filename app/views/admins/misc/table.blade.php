


                            
<div class="table-responsive">  
  <table id="{{$id}}" class="table table-striped table-hover">
    <thead>
      @foreach ($heads as $head)
        <th>{{$head}}</th>
      @endforeach   
    </thead>
    <tbody>
      @if (empty($rows)) 
        <tr class="no-content">
          <td class="text-center" colspan="{{count($heads)}}">No content</td>
        </tr>
      @else
        @foreach ($rows as $row)    
          {{$row}}     
        @endforeach
      @endif
    </tbody>
  </table> 
</div>