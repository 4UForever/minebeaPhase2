<div class="table-responsive">
  <table id="{{$id}}" class="table table-striped table-hover" data-url="{{$url}}">
    <thead>
      <tr>
        @foreach ($headers as $header)
          <th>{{$header}}</th>
        @endforeach
      </tr>
    </thead>
    @if (!empty($filters))
    <thead>
      <tr>
      @foreach ($headers as $col=>$header)
        <td style="padding:2px;";>{{(!empty($filters[$col])) ? $filters[$col]:"";}}</td>
      @endforeach
      </tr>
    </thead>
    @endif
    <tfoot>
      <tr>
        @foreach ($headers as $header)
          @if (strpos($header, "checkbox"))
            <th></th>
          @else
            <th>{{$header}}</th>
          @endif
        @endforeach
      </tr>
    </thead>
  </table>
</div>
