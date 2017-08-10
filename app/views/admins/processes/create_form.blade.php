



<div class="page-header clearfix">
  <h3>
    <span class="glyphicon glyphicon-wrench"></span>
    <span class="glyphicon-class">PROCESS</span>
  </h3>
</div>
<form id="new-process-form" role="form" data-validaton-url="{{url('admin/process/create-form/validate')}}">
  {{Form::token()}}
  <div class="col-xs-12">  
    <input type="hidden" name="id" value="{{$id}}">                                                                            
    <div class="form-group">              
      <label for="title">Title</label>
      <input type="text" name="title" class="form-control" id="title" value="{{$title}}" placeholder="Enter a process title here">
    </div>                                                                                 
    <div class="form-group">              
      <label for="number">Number</label>
      <input type="text" name="number" class="form-control" id="number" value="{{$number}}" placeholder="Enter a process number here">
    </div>                                                                                                                                          
    <div class="form-group"> 
      <div class="text-center">                              
        <input type="submit" id="submit" class="btn btn-success btn-submit" value="SUBMIT">
      </div>                                                            
    </div>   
  </div>                                                                     
</form>