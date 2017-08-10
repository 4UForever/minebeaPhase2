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
    <h3>Add New User</h3>
  </div>

  <div class="row">
    <div class="col-xs-12">
      <form role="form" method="POST" action="{{url("admin/user/create")}}">
        {{Form::token()}}
        <div class="form-group">
          <label for="group">Group</label>
          <select class="chosen-select form-control" name="group_id" data-placeholder="Choose a group">
            @foreach ($groups as $group)
              @if ($group->id == Input::old('group_id'))
                <option value="{{$group->id}}" selected="selected">{{$group->name}}</option>
              @else
                <option value="{{$group->id}}">{{$group->name}}</option>
              @endif
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label for="group">Processes</label>
          <select class="chosen-select form-control" multiple="multiple" name="process_ids[]" data-placeholder="Choose at least one process">
            @foreach ($processes as $process)
              @if (in_array($process->id, Input::old('process_ids', array())))
                <option value="{{$process->id}}" selected="selected">{{$process->title}}</option>
              @else
                <option value="{{$process->id}}">{{$process->title}}</option>
              @endif
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="text" name="email" class="form-control" placeholder="Email" id="email" value="{{Input::old('email', '')}}">
        </div>
        <div class="form-group">
          <label for="qr_code">QR code</label>
          <input type="text" name="qr_code" class="form-control" placeholder="QR code" id="qr_code" value="{{Input::old('qr_code', '')}}">
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Password" id="password" value="{{Input::old('password', '')}}">
        </div>
        <div class="form-group">
          <label for="password_confirmation">Password confirmation</label>
          <input type="password" name="password_confirmation" class="form-control" placeholder="Password confirmation" id="password_confirmation" value="{{Input::old('password_confirmation', '')}}">
        </div>
        <div class="form-group">
          <label for="first_name">First name</label>
          <input type="text" name="first_name" class="form-control" placeholder="First name" id="first_name" value="{{Input::old('first_name', '')}}">
        </div>
        <div class="form-group">
          <label for="last_name">Last name</label>
          <input type="text" name="last_name" class="form-control" placeholder="Last name" id="last_name" value="{{Input::old('last_name', '')}}">
        </div>
        <div class="form-group">
          <label for="leader">Line leader</label>
          <div class="form-control">
            <label class="radio-inline"><input type="radio" name="leader" value="1"{{(Input::old('leader')=="1")? " checked":"";}}> Leader</label>&nbsp;&nbsp;
            <label class="radio-inline"><input type="radio" name="leader" value="0"{{(Input::old('leader')=="0")? " checked":"";}}> Staff</label>
          </div>
        </div>
        <div class="form-group">
          <div class="text-center">
            <input type="submit" class="btn btn-success btn-submit" value="SUBMIT">
          </div>
        </div>
      </form>
    </div>
  </div>
@stop