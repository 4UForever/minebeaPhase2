@extends('layouts.admin')

@section('css')
  @parent
@stop

@section('js')
  @parent
@stop

@section('content')
<div class="row">
	<div class="well col-md-12">
		{{ $str }}
	</div>
</div>
@stop