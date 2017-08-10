@extends('layouts.admin')

@section('css') 
  @parent
@stop

@section('js')   
  @parent      
  <script src="{{asset('assets/js/activity.js')}}"></script>                                            
@stop

@section('content')   
  
<div class="page-header clearfix">
  <div class="row">
    <div class="col-xs-6">    
      <h3>
        <span class="glyphicon glyphicon-edit"></span>
        <span class="glyphicon-class">
          ACTIVITY
        </span>
      </h3>
    </div>   
  </div>   
</div>   
               
{{$datatable}}
          
@stop   