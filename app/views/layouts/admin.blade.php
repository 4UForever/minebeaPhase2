<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Minebea Co., Ltd.</title>

  @section('css')
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/bootstrap-theme.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/chosen/chosen.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/chosen/chosen.bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('assets/data_table/css/dataTables.bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('assets/colorbox/colorbox.css')}}" rel="stylesheet">

    <link href="{{asset('assets/css/style.css')}}" rel="stylesheet">
  @show

  @section('js')
    <script src="{{asset('assets/js/jquery-1.11.1.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('assets/js/admin.chosen.js')}}"></script>
    <script src="{{asset('assets/data_table/js/jquery.dataTables.js')}}"></script>
    <script src="{{asset('assets/data_table/js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/colorbox/jquery.colorbox-min.js')}}"></script>
  @show
</head>

<body>
  <div class="wrapper" id="top" >
    <header id="header">
      <div class="row">
        <div class="col-xs-6">
          <div id="logo">
            <a href="{{url('admin')}}"><img src="{{asset('assets/images/logo.png')}}" alt=""></a>
          </div>
        </div>
        <div class="col-xs-6">
          <div class="right-box clearfix">
            <div class="pull-right">
              <span class="logout">
                <a href="{{url('admin/user/logout')}}" class="btn btn-logout btn-md">
                  <span class="glyphicon glyphicon-logout-icon"></span>
                  <span class="glyphicon-class">Logout</span>
                </a>
              </span>
            </div>
          </div>
        </div>
      </div>
    </header>
    <div class="page clearfix">
      <div id="sidebar">
        <div class="sidenav">
          <ul>
            <!--
            <li>
              <a {{Request::segment(2) == 'activity' ? 'class="active"' : ''}}  href="{{url("admin/activity")}}">
                <span class="glyphicon glyphicon-edit"></span>
                <span class="glyphicon-class">Activity</span>
              </a>
            </li>
            -->
            <li>
              <a {{Request::segment(2) == 'line' ? 'class="active"' : ''}}  href="{{url("admin/line")}}">
                <span class="glyphicon glyphicon-cog"></span>
                <span class="glyphicon-class">Production line</span>
              </a>
            </li>
            <li>
              <a {{Request::segment(2) == 'model' ? 'class="active"' : ''}}  href="{{url("admin/model")}}">
                <span class="glyphicon glyphicon-tags"></span>
                <span class="glyphicon-class">Model</span>
              </a>
            </li>
            <li>
              <a {{Request::segment(2) == 'process' ? 'class="active"' : ''}}  href="{{url("admin/process")}}">
                <span class="glyphicon glyphicon-wrench"></span>
                <span class="glyphicon-class">Process</span>
              </a>
            </li>
            <li>
              <a {{Request::segment(2) == 'document-category' ? 'class="active"' : ''}}  href="{{url("admin/document-category")}}">
                <span class="glyphicon glyphicon-folder-close"></span>
                <span class="glyphicon-class">Document category</span>
              </a>
            </li>
            <li>
              <a {{Request::segment(2) == 'document' ? 'class="active"' : ''}}  href="{{url("admin/document")}}">
                <span class="glyphicon glyphicon-list-alt"></span>
                <span class="glyphicon-class">Document</span>
              </a>
            </li>
            <li>
              <a {{Request::segment(2) == 'user' ? 'class="active"' : ''}}  href="{{url("admin/user")}}">
                <span class="glyphicon glyphicon-user"></span>
                <span class="glyphicon-class">User</span>
              </a>
            </li>
            <li>
              <a {{Request::segment(2) == 'group' ? 'class="active"' : ''}}  href="{{url("admin/group")}}">
                <span class="glyphicon glyphicon-th"></span>
                <span class="glyphicon-class">User group</span>
              </a>
            </li>
            <li>
              <a {{Request::segment(2) == 'wip' ? 'class="active"' : ''}}  href="{{url("admin/wip")}}">
                <span class="glyphicon glyphicon-signal"></span>
                <span class="glyphicon-class">Condition (WIP)</span>
              </a>
            </li>
            <li>
              <a {{Request::segment(2) == 'lot' ? 'class="active"' : ''}}  href="{{url("admin/lot")}}">
                <span class="glyphicon glyphicon-tasks"></span>
                <span class="glyphicon-class">Lot (WIP)</span>
              </a>
            </li>
            <li>
              <a {{Request::segment(2) == 'part' ? 'class="active"' : ''}}  href="{{url("admin/part")}}">
                <span class="glyphicon glyphicon-road"></span>
                <span class="glyphicon-class">Part</span>
              </a>
            </li>
            <li>
              <a {{Request::segment(2) == 'iqc-lot' ? 'class="active"' : ''}}  href="{{url("admin/iqc-lot")}}">
                <span class="glyphicon glyphicon-hdd"></span>
                <span class="glyphicon-class">IQC Lot</span>
              </a>
            </li>
            <li>
              <a {{Request::segment(2) == 'ng-detail' ? 'class="active"' : ''}}  href="{{url("admin/ng-detail")}}">
                <span class="glyphicon glyphicon-inbox"></span>
                <span class="glyphicon-class">NG List</span>
              </a>
            </li>
            <li>
              <a {{Request::segment(2) == 'break' ? 'class="active"' : ''}}  href="{{url("admin/break")}}">
                <span class="glyphicon glyphicon-pause"></span>
                <span class="glyphicon-class">Break reason</span>
              </a>
            </li>
            <li>
              <a {{Request::segment(2) == 'process-log' ? 'class="active"' : ''}}  href="{{url("admin/process-log")}}">
                <span class="glyphicon glyphicon-time"></span>
                <span class="glyphicon-class">Process log</span>
              </a>
            </li>
            <li>
              <a {{Request::segment(2) == 'process-working' ? 'class="active"' : ''}}  href="{{url("admin/process-working")}}">
                <span class="glyphicon glyphicon-exclamation-sign"></span>
                <span class="glyphicon-class">Process working</span>
              </a>
            </li>
            <li>
              <a {{Request::segment(2) == 'report-import' ? 'class="active"' : ''}}  href="{{url("admin/report-import")}}">
                <span class="glyphicon glyphicon-folder-open"></span>
                <span class="glyphicon-class">Import data report</span>
              </a>
            </li>
            <li>
              <a {{Request::segment(2) == 'report-daily' ? 'class="active"' : ''}}  href="{{url("admin/report-daily")}}">
                <span class="glyphicon glyphicon-book"></span>
                <span class="glyphicon-class">Report daily</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div id="content">
        <div class="container">
          @if (Session::has('success'))
            <p></p>
              <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p>{{Session::get('success')}}</p>
              </div>
          @endif
          @if ($errors->any())
            <p></p>
              <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                @foreach ($errors->all() as $error)
                  <p>{{$error}}</p>
                @endforeach
              </div>
          @endif
          @yield('content')
        </div>
      </div>
      <div class="gotop">
        <a class="btn btn-gotop" href="#top">
          <span class="glyphicon glyphicon-arrow-up"></span>
          <span class="glyphicon-class">TOP</span>
        </a>
      </div>
    </div>
  </div>
</body>

</html>