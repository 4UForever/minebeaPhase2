@extends('layouts.admin')

@section('css') 
  @parent
  <link href="{{asset('assets/css/chosen/chosen.min.css')}}" rel="stylesheet">
  <link href="{{asset('assets/css/chosen/chosen.bootstrap.css')}}" rel="stylesheet">
@stop

@section('js')   
  @parent             
  <script src="{{asset('assets/js/chosen.jquery.min.js')}}"></script>                                 
  <script src="{{asset('assets/js/admin.chosen.js')}}"></script>                                      
@stop

@section('content')   
  <div class="page-header clearfix">
    <h3><span class="glyphicon glyphicon-wrench"></span>
      <span class="glyphicon-class">NEW PROCESS</span>
    </h3>
  </div>
  <div class="row">                                                                   
    <form role="form" method="POST" action="{{url("admin/process/create")}}">
      {{Form::token()}}
      <div class="col-xs-12">                                                                              
        <div class="form-group">              
          <label for="title">Title</label>
          <input type="text" name="title" class="form-control" id="title" value="{{Input::old('title', '')}}">
        </div>                                                                              
        <div class="form-group">              
          <label for="number">Number</label>
          <input type="text" name="number" class="form-control" id="number" value="{{Input::old('number', '')}}">
        </div>    
        <div class="form-group">              
          <label for="group">Line</label>   
          <select class="chosen-select form-control" name="line_id" data-placeholder="Choose a line">    
            @foreach ($lines as $line)                 
              @if ($line->id == Input::old('line_id'))        
                <option value="{{$line->id}}" selected="selected">{{$line->title}}</option>
              @else
                <option value="{{$line->id}}">{{$line->title}}</option>
              @endif
            @endforeach
          </select>
        </div>    
        <div class="form-group">              
          <label for="user_ids[]">User</label>   
          <select class="chosen-select form-control" multiple="multiple" name="user_ids[]" data-placeholder="Choose at least one user">    
            @foreach ($users as $user)
              @if (in_array($user->id, Input::old('user_ids', array())))        
                <option value="{{$user->id}}" selected="selected">{{$user->first_name}} {{$user->last_name}}</option>
              @else
                <option value="{{$user->id}}">{{$user->first_name}} {{$user->last_name}}</option>
              @endif
            @endforeach
          </select>
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