



<div class="page-header clearfix">
  <h3>
    <span class="glyphicon glyphicon-wrench"></span>
    <span class="glyphicon-class">MODEL</span>
  </h3>
</div>
<form id="new-product-form" role="form" data-validaton-url="{{url('admin/model/create-form/validate')}}">
  {{Form::token()}}
  <div class="col-xs-12">                                                                              
    <div class="form-group">              
      <label for="title">Title</label>
      <input type="text" name="title" class="form-control" id="title" value="{{$title}}" placeholder="Enter a model title here">
    </div>                                                                                                                                         
    <div class="form-group"> 
      <div class="text-center">                              
        <input type="submit" id="submit" class="btn btn-success btn-submit" value="SUBMIT">
      </div>                                                            
    </div>   
  </div>                                                                     
</form>