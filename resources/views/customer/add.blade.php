@extends('layouts.master')
@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col">
                <div class="page-header-left">
                    <h3>Create Electrician</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{Request::root()}}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{Request::root()}}/employee">Electricians</a></li>
                        <li class="breadcrumb-item active">Create Electrician</li>
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
                    <h5>Create Electrician</h5>
                </div>
                <form action="{{Request::root()}}/employee/store" method="POST" id="user-form" enctype="multipart/form-data">
                    @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-2">
                                <div class="col-form-label">Employee Unique ID <span class="red-star">*</span></div>
                                <input type="text" class="form-control required_field" name="employee_unique_id" id="employee_unique_id" placeholder="Employee Unique ID" autocomplete="off" value="{{old('employee_unique_id')}}"/>
                            </div>
                            @if ($errors->has('employee_unique_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('employee_unique_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-2">
                                <div class="col-form-label">Electrician <span class="red-star">*</span></div>
                                <input type="text" class="form-control required_field" name="name" id="name" placeholder="Electrician Name" autocomplete="off" value="{{old('name')}}" />
                            </div>
                            @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-2">
                                <div class="col-form-label">Mobile Number <span class="red-star">*</span></div>
                                <input type="number" class="form-control required_field" maxlength="10" name="mobile_no" id="mobile_no" placeholder="Mobile Number" autocomplete="off" value="{{old('mobile_no')}}" />
                            </div>
                            @if ($errors->has('mobile_no'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('mobile_no') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3 m-t-15">
                                <div class="form-check checkbox checkbox-primary mb-0">
                                    <input class="form-check-input" id="whatsapp_same_mobile_no" type="checkbox" value="1" name="whatsapp_same_mobile_no" checked>
                                    <label class="form-check-label" for="whatsapp_same_mobile_no">WhatsApp No. Same as Mobile No.</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 whatsapp_no_section" @if (!$errors->has('whatsapp_no')) style="display:none" @endif>
                            <div class="mb-2">
                                <div class="col-form-label">WhatsApp No.</div>
                                <input type="number" class="form-control" maxlength="10" name="whatsapp_no" id="whatsapp_no" placeholder="WhatsApp Number" autocomplete="off" value="{{old('whatsapp_no')}}" />                         
                            </div>
                            @if ($errors->has('whatsapp_no'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('whatsapp_no') }}</strong>
                                </span>
                            @endif
                        </div>

                    </div>

                   
                    <!-- <div class="row">
                    </div> -->

                    <div class="row">
                        
                        <div class="col-lg-12">
                            <div class="mb-2">
                                <div class="col-form-label">Address</div>
                                <input type="text" class="form-control required_field"  name="address" id="address" placeholder="Address" autocomplete="off" value="{{old('address')}}" />                         
                            </div>
                            @if ($errors->has('address'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('address') }}</strong>
                                </span>
                            @endif
                        </div>

                        <!-- <div class="col-lg-12">
                            <div class="mb-2">
                                <div class="col-form-label">Description</div>
                                <textarea class="form-control" name="description" id="description" placeholder="Description">{{old('description')}}</textarea>
                            </div>
                            @if ($errors->has('description'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('description') }}</strong>
                                </span>
                            @endif
                        </div> -->

                        
                      
                    </div>

                    <div class="row">

                        <div class="col-lg-6">
                            <div class="mb-2">
                                <div class="col-form-label">Expertise <span class="red-star">*</span></div>
                                <select class="form-select js-example-basic-multiple required_field" name="expertise[]" id="expertise" multiple="multiple">
                                    <option value="">Select Expertise</option>
                                    @if(isset($skillslists) && count($skillslists)>0)
                                        @foreach($skillslists as $skills)
                                            <option value="{{$skills->skill_id}}">{{$skills->skill_name}}</option>
                                        @endforeach
                                    @endif
                                </select>                            
                            </div>
                            @if ($errors->has('expertise'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('expertise') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-2">
                                <div class="col-form-label">Market Place <span class="red-star">*</span></div>
                                <select class="form-select required_field" name="market_id" id="market_id">
                                    <option value="">Select Market Place</option>
                                </select>                            
                            </div>
                            @if ($errors->has('market_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('market_id') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-2">
                                <div class="col-form-label">Region <span class="red-star">*</span></div>
                                <input type="hidden" class="form-control required_field" name="region_id" id="region_id" placeholder="Mobile Number" autocomplete="off" value="{{old('region_id')}}" readonly/>
                                <input type="text" class="form-control required_field" name="region_name" id="region_name" placeholder="Region" autocomplete="off" value="{{old('region_name')}}" readonly/>

                                <!-- <select class="form-select js-example-basic-single required_field" name="region_id" id="state_region_id">
                                    <option value="">Select Region</option>
                                    @if(isset($regions) && count($regions)>0)
                                        @foreach($regions as $key=>$region)
                                            <option value="{{$region->region_id}}" {{ (old('region_id') == $region->region_id) ? 'selected' : ''}}>{{$region->region_name}}</option>
                                        @endforeach
                                    @endif 
                                </select> -->
                            </div>
                            @if ($errors->has('region_name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('region_name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-2">
                                <div class="col-form-label">State <span class="red-star">*</span></div>
                                <input type="hidden" class="form-control required_field" name="state_id" id="market_state_id" value="{{old('state_id')}}"  readonly/>
                                <input type="text" class="form-control required_field" name="state_name" id="market_state_name" placeholder="State" autocomplete="off" value="{{old('state_name')}}" readonly/>

                                <!-- <select class="form-select js-example-basic-single required_field" name="state_id" id="state_id">
                                    <option value="">Select State</option>
                                </select> -->
                            </div>
                            @if ($errors->has('state_name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('state_name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-2">
                                <div class="col-form-label">City <span class="red-star">*</span></div>
                                <input type="hidden" class="form-control required_field" name="city_id" id="market_city_id" value="{{old('city_id')}}" readonly/>
                                <input type="text" class="form-control required_field" name="city_name" id="market_city_name" placeholder="City" autocomplete="off" value="{{old('city_name')}}" readonly/>

                                <!-- <select class="form-select js-example-basic-single required_field" name="city_id" id="city_id">
                                    <option value="">Select City</option>
                                </select> -->
                            </div>
                            @if ($errors->has('market_city_name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('market_city_name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-2">
                                <div class="col-form-label">Pincode <span class="red-star">*</span></div>
                                <input type="text" class="form-control required_field" name="pincode" id="market_pincode" placeholder="Pincode" autocomplete="off" value="{{old('pincode')}}" readonly/>
                            </div>
                            @if ($errors->has('pincode'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('pincode') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-2">
                                <div class="col-form-label">Profile Picture </div>
                                <input type="file" class="form-control" name="logo_file" id="image_profile" value="{{old('logo')}}" />
                                <input type="text" name="logo" id="cropped_val" hidden>
                            </div>
                           
                            @if ($errors->has('logo'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('logo') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-6"></div>

                        <div class="col-lg-6 cropie_section" style="display:none"> 
                            <div class="mb-2">
                                <div id="upload-demo" class="profile"></div>
                                <button id="crop-profile" type="button" class="btn btn-primary mr-1 mb-1">Crop</button>
                            </div>
                        </div>

                        <div class="col-lg-6 cropie_section" style="display:none">
                            <div class="mb-2">
                                <img id="cropped_img" src="" alt="" >
                            </div>
                        </div>

                    </div>
        
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-pill btn-success btn-air-success btn-air-success" data-original-title="" title=""><span>Submit</span>
                        </button>

                        <a href="{{Request::root()}}/employee">
                            <button type="button" class="btn btn-pill btn-danger btn-air-danger active"><span>Cancel</span>
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