@extends('layouts.admin')

@section('css') 
  @parent
@stop

@section('js')   
  @parent                                                 
@stop

@section('content')   
  <div class="page-header clearfix">
    <h3><span class="glyphicon glyphicon-th"></span>
      <span class="glyphicon-class">EDIT GROUP: <i>{{$group->name}}</i></span>
    </h3>
  </div>
  <div class="row">                                                                   
    <form role="form" method="POST" action="{{url("admin/group/{$group->id}/update")}}">
      {{Form::token()}}
      <div class="col-xs-12">
        <div class="form-group">              
          <label for="name">Title</label>
          <input type="text" name="name" class="form-control" id="name" value="{{$group->name}}">
        </div>      
        <label>Permissions</label> 
        <div class="permission-set">            
          @foreach ($permissions as $permission) 
            <div class="form-group">
              <input type="checkbox" name="permission_ids[{{$permission->key}}]" id="permission_ids[{{$permission->key}}]" {{empty($group_permissions[$permission->key]) ? '' : 'checked="checked"'}}>
              <label for="permission_ids[{{$permission->key}}]">{{$permission->title}}</label>                                                                     
            </div>
          @endforeach 
        </div>                                                                                                                               
        <div class="form-group"> 
          <div class="text-center">                              
            <input type="submit" class="btn btn-success btn-submit" value="SUBMIT">                                        
          </div>                                                            
        </div>   
      </div>                                                                       
    </form>
  </div>  
@stop   