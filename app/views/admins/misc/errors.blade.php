

             
             
<div class="alert alert-danger">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  @foreach ($errors as $error)
    <p>{{$error}}</p>
  @endforeach
</div>