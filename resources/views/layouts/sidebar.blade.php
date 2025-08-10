<?php
$roles = config('constants.ROLES');
?>
<div class="page-sidebar">
   <div class="sidebar custom-scrollbar">
      <div class="sidebar-user text-center">
         <h6 class="mt-3 f-14">{{Auth::user()->name}}</h6>
         <p>{{$roles[Auth::user()->user_role]}}</p>
      </div>
      <ul class="sidebar-menu">
         <li class="active"><a class="sidebar-header" href="{{Request::root()}}/home"><i data-feather="home"></i><span> Dashboard</span></a></li>
         @if($roles[Auth::user()->user_role] != 'User')
         <li class = "{{ Request::segment(1) === 'users' ? 'active' : null }}">
            <a class="sidebar-header" href="#"><i data-feather="settings"></i><span>Users</span><i class="fa fa-angle-right pull-right"></i></a>
            <ul class="sidebar-submenu {{ Request::segment(1) === 'users' ? 'menu-open' : null }}">
               <li class= "{{ Request::segment(1) === 'users' && (null == Request::segment(2)) ? 'active' : null }}"><a href="{{Request::root()}}/users"><i class="fa fa-circle"></i><span>List Users</span></a></li>
            </ul>
         </li>
         @endif
         
         <li class = "{{ Request::segment(1) === 'customer' ? 'active' : null }}">
            <a class="sidebar-header" href="#"><i data-feather="settings"></i><span>Customers</span><i class="fa fa-angle-right pull-right"></i></a>
            <ul class="sidebar-submenu {{ Request::segment(1) === 'customer' ? 'menu-open' : null }}">
               <li class= "{{ Request::segment(1) === 'customer' && (null == Request::segment(2)) ? 'active' : null }}"><a href="{{Request::root()}}/customer"><i class="fa fa-circle"></i><span>List Customers</span></a></li>
            </ul>
         </li>

         <li><a class="sidebar-header" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i data-feather="log-out"></i><span> Logout</span> </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
               @csrf
            </form>
         </li>

      </ul>
   </div>
</div>
