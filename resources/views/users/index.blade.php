@extends('layouts.master')
@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col">
                <div class="page-header-left">
                    <h3>Users</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{Request::root()}}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
            <!-- Bookmark Start-->
            <div class="col">
                <div class="bookmark pull-right">
                </div>
            </div>
            <!-- Bookmark Ends-->
        </div>
    </div>
</div>
@include('layouts.alert')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    @if(count($users)>0)
                    <table class="table display table-bordered table-striped table-hover">
                        <tbody>
                            <tr>
                                <th class="sort_function" data-order_by="ASC" data-value_search="name">NAME</th>
                                <th class="sort_function" data-order_by="ASC" data-value_search="email">EMAIL</th>
                                <th class="center">ROLE</th>
                            </tr>
                            @foreach ($users as $key => $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ ($user->user_role == 2) ? 'Admin' : 'User' }}</td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                    @else
                    <div class="padding">
                        <div class="alert alert-danger dark alert-dismissible fade show" role="alert"><i class="icon-thumb-down"></i><strong>Oops ! </strong>Not Data Found
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">            
                    Showing  {{$users->count()}} of {{$users->total()}} Entries
                    <div class="pagination_tab">
                        {{$users->appends(request()->query())->links()}}
                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
