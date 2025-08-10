@extends('layouts.master')
@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col">
                <div class="page-header-left">
                    <h3>Show Customer</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{Request::root()}}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{Request::root()}}/customer">Customer</a></li>
                        <li class="breadcrumb-item active">Show Customer</li>
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
                    <h5>CUSTOMER: {{ $customer_data->first_name }} {{ $customer_data->last_name }}</h5>
                    <hr>
                    <div class="col-sm-12">  
                        <div class="profile-img-style">
                            @if(isset($customer_data))
                                <table class="table display table-bordered table-striped table-hover">
                                    <tbody>
                                        <tr>
                                            <th>Last Name</th>
                                            <td>{{ $customer_data->last_name }}</td>
                                            <th>Gender</th>
                                            <td>{{ $customer_data->gender }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $customer_data->email }}</td>
                                            <th>Phone Number</th>
                                            <td>{{ $customer_data->phone_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>Date of Birth</th>
                                            <td>{{ $customer_data->date_of_birth }}</td>
                                            <th>Anniversary</th>
                                            <td>{{ $customer_data->anniversary }}</td>
                                        </tr>
                                        <tr>
                                            <th >Address Line</th>
                                            <td colspan="3">{{ $customer_data->address_line }}</td>
                                        </tr>
                                        <tr>
                                            <th>Address Landmark</th>
                                            <td>{{ $customer_data->address_landmark }}</td>
                                            <th>Country Code</th>
                                            <td>{{ $customer_data->country_code }}</td>
                                        </tr>
                                        <tr>
                                            <th>Invoice Type</th>
                                            <td>{{ $customer_data->invoice_type }}</td>
                                            <th>Allow Promotional Communication</th>
                                            <td>{{ $customer_data->allow_promotional_communication == 1 ? 'Yes' : 'No' }}</td>
                                            
                                        </tr>
                                        <tr>
                                            <th>Allow Transactional Communication</th>
                                            <td>{{ $customer_data->allow_transactional_communication == 1 ? 'Yes' : 'No' }}</td>
                                            <th>Communication Channel</th>
                                            <td>{{ $customer_data->communication_channels }}</td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                                @else
                                <div class="padding">
                                    <div class="alert alert-danger dark alert-dismissible fade show" role="alert"><i class="icon-thumb-down"></i><strong>Oops ! </strong>Not Data Found
                                    </div>
                                </div>
                            @endif


                            


                            
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="{{Request::root()}}/customer">
                            <button type="button" class="btn btn-pill btn-danger btn-air-danger active"><span>Back</span>
                            </button>
                            </a>
                    </div>


                </div>
                
            </div>
        </div>
    </div>
</div>
@stop