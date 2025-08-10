@extends('layouts.master')
@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col">
                <div class="page-header-left">
                    <h3>Customers</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{Request::root()}}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item active">Customers</li>
                    </ol>
                </div>
            </div>
            <!-- Bookmark Start-->
            <div class="col">
                <div class="bookmark pull-right">
                    <ul>
                        <li><a href="{{Request::root()}}/customer/import" data-container="body" data-toggle="popover" data-placement="top" title="Import Customers" data-original-title="Import Customers"><i data-feather="upload"></i></a></li>
                        <!-- <li><a href="{{Request::root()}}/customer/export" data-container="body" data-toggle="popover" data-placement="top" title="Export Customers" data-original-title="Export Customers"><i data-feather="download"></i></a></li> -->
                        <!-- <li><a href="{{Request::root()}}/customer/create" data-container="body" data-toggle="popover" data-placement="top" title="Add Electrician" data-original-title="Add Electrician"><i data-feather="file-plus"></i></a></li> -->
                        <!-- <li>
                            <a href="#"><i class="bookmark-search" data-feather="search"></i></a>
                            <form class="form-inline search-form" action="{{Request::root()}}/customer" method="GET">
                                <div class="form-group form-control-search">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="<?php echo isset($_GET["search"]) ? $_GET["search"] : ""; ?>">
                                </div>
                            </form>
                        </li> -->
                    </ul>
                </div>
            </div>
            <!-- Bookmark Ends-->
        </div>
    </div>
</div>

@include('customer.search')

@include('layouts.alert')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    @if(count($customerlist)>0)
                    <table class="table display table-bordered table-striped table-hover">
                        <tbody>
                            <tr>
                                <th class="sort_function" data-order_by="ASC" data-value_search="customers.id">Sr No</th>
                                <th class="sort_function" data-order_by="ASC" data-value_search="customers.first_name">NAME</th>
                                <th class="sort_function" data-order_by="ASC" data-value_search="customers.email">EMAIL</th>
                                <th class="sort_function" data-order_by="ASC" data-value_search="customers.phone">PHONE</th> 
                                <th class="center">ACTION</th>
                            </tr>
                            @foreach ($customerlist as $key => $customer)
                            <tr>
                                <td>{{ $customer->id }}</td>
                                <td>{{ $customer->first_name }} {{ $customer->last_name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone_number }}</td>
                                <td class="center">
                                    <a href="{{Request::root()}}/customer/show/{{$customer->id}}" data-original-title="" title="">
                                        <span class="icon-size"><i class="fa fa-eye"></i></span>
                                    </a>
                                </td>
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
                    Showing  {{$customerlist->count()}} of {{$customerlist->total()}} Entries
                    <div class="pagination_tab">
                        {{$customerlist->appends(request()->query())->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
