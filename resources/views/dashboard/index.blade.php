@extends('layouts.master')
@section('content')
<div class="container-fluid">
  <div class="page-header">
    <div class="row">
      <div class="col">
        <div class="page-header-left">
          <h3>Dashboard</h3>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.html"><i data-feather="home"></i></a></li>
            <li class="breadcrumb-item">Dashboard</li>
          </ol>
        </div>
      </div>
      <!-- Bookmark Start-->
      <div class="col">
        <div class="bookmark pull-right">
          <ul>
        
          </ul>
        </div>
      </div>
      <!-- Bookmark Ends-->
    </div>
  </div>
</div>
<!-- Container-fluid starts-->
<div class="container-fluid">
  <div class="row">
    <div class="col-xl-12 xl-100">
      <div class="row">
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="chart-widget-dashboard">
                <div class="media">
                  <div class="media-body">
                    <h5 class="mt-0 mb-0 f-w-600"><a href="{{Request::root()}}/customer" target="_blank"><span class="counter">{{$custcount}}</span></a></h5>
                    <p>Total Customers</p>
                  </div><i data-feather="tag"></i>
                </div>
                <div class="dashboard-chart-container">
                  <div class="small-chart-gradient-1"></div>
                </div>
              </div>
            </div>
          </div>
        </div>




        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="chart-widget-dashboard">
                <div class="media">
                  <div class="media-body">
                    <h5 class="mt-0 mb-0 f-w-600"><a href="{{Request::root()}}/users" target="_blank"><span class="counter">{{$admincount}}</span></a></h5>
                    <p>Total Admin Users</p>
                  </div><i data-feather="tag"></i>
                </div>
                <div class="dashboard-chart-container">
                  <div class="small-chart-gradient-2"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="chart-widget-dashboard">
                <div class="media">
                  <div class="media-body">
                    <h5 class="mt-0 mb-0 f-w-600"><a href="{{Request::root()}}/users" target="_blank"><span class="counter">{{$usercount}}</span></a></h5>
                    <p>Total User</p>
                  </div><i data-feather="tag"></i>
                </div>
                <div class="dashboard-chart-container">
                  <div class="small-chart-gradient-2"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection