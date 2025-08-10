@extends('layouts.master')
@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col">
                <div class="page-header-left">
                    <h3>Edit User</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{Request::root()}}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{Request::root()}}/users">Users</a></li>
                        <li class="breadcrumb-item active">Edit User</li>
                    </ol>
                </div>
            </div>
            <!-- Bookmark Start-->
            <!-- Bookmark Ends-->
        </div>
    </div>
</div>
<div class="container-fluid select2-drpdwn">
    <div class="row">
        <div class="col-sm-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5>Edit Users</h5>
                </div>
                <form action="{{Request::root()}}/users/update" method="POST" id="user-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" value="{{$user_data->id}}"/>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-2">
                                <div class="col-form-label">Name <span class="red-star">*</span></div>
                                <input type="text" class="form-control required_field" name="name" id="name" placeholder="Name" autocomplete="off" value="{{$user_data->name}}" />
                            </div>
                            @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-2">
                                <div class="col-form-label">Email <span class="red-star">*</span></div>
                                <input type="email" class="form-control required_field" name="email" id="email" placeholder="Email" autocomplete="off" value="{{$user_data->email}}" />
                            </div>
                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-2">
                                <div class="col-form-label">Role <span class="red-star">*</span></div>
                                <select class="form-select js-example-basic-single required_field" name="role" id="role">
                                    <option value="">Select Role</option>
                                    @if(isset($roles) && count($roles)>0)
                                        @foreach($roles as $key=>$role)
                                            @if($role->name !== 'Super Admin')
                                                <option value="{{$role->id}}" {{ ($user_data->user_role == $role->id) ? 'selected' : ''}}>{{$role->name}}</option>
                                            @endif
                                        @endforeach
                                    @endif 
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <button class="btn btn-square btn-outline-warning update_password" type="button" title="" data-bs-original-title="btn btn-square btn-outline-warning" data-original-title="btn btn-square btn-outline-success">Change Password</button>
                        </div>
                    </div>
                    <div class="row change_password_section"  @if (!$errors->has('password') && !$errors->has('confirm_password')) style="display:none" @endif>
                            <div class="col-lg-6">
                                <div class="mb-2">
                                    <div class="col-form-label">Password <span class="red-star">*</span></div>
                                    <input type="Password" class="form-control" name="password" id="password" placeholder="Password" autocomplete="off" value="" />
                                </div>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-2">
                                    <div class="col-form-label">Confirm Password <span class="red-star">*</span></div>
                                    <input type="Password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" autocomplete="off" value="" />
                                </div>
                                @if ($errors->has('confirm_password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('confirm_password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>
        
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-pill btn-success btn-air-success btn-air-success" data-original-title="" title=""><span>Update</span>
                            <!-- <div class="icon"><i class="fa fa-send-o" aria-hidden="true"></i></div> -->
                        </button>

                        <a href="{{Request::root()}}/users">
                            <button type="button" class="btn btn-pill btn-danger btn-air-danger active"><span>Cancel</span>
                                <!-- <div class="icon"><i class="fa fa-close" aria-hidden="true"></i></div> -->
                            </button>
                            </a>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop