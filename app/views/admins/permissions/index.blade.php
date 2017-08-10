@extends('layouts.admin')

@section('css') 
  @parent
@stop

@section('js')   
  @parent 
  <script src="{{asset('assets/js/permission.js')}}"></script>     
@stop

@section('content')  
  <div class="page-header clearfix">
    <div class="col-xs-6">    
      <h3>
        <span class="glyphicon glyphicon-ok-circle"></span>
        <span class="glyphicon-class">Permissions</span>
      </h3>
    </div>
    <div class="col-xs-6">  
      <input id="permissions-form-submit" type="submit" value="Save configuration" class="btn btn-success pull-right">
    </div>
  </div> 
  <form action="{{url('admin/permission')}}" method="POST" role="form" id="permissions-form">
    {{Form::token()}}
    <div class="table-responsive">  
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Permission</th>
            @foreach ($groups as $group)
              <th class="text-center">{{$group->name}}</th>
            @endforeach                                                 
          </tr>
        </thead>
        @foreach ($permissions as $permission)
          <tr>           
            <td>{{$permission->title}}</td>                                 
            @foreach ($groups as $group)
              <td class="text-center"> 
                @if (empty($group->getPermissions()[$permission->key]))
                  <input type="checkbox" name="permissions[{{$group->id}}][{{$permission->key}}]"/>
                @else
                  <input type="checkbox" checked="checked" name="permissions[{{$group->id}}][{{$permission->key}}]"/>
                @endif
              </td>
            @endforeach              
          </tr>
        @endforeach
      </table>               
    </div> 
  </form>
@stop