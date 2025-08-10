@extends('layouts.master')
@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col">
                <div class="page-header-left">
                    <h3>Import Customers</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{Request::root()}}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{Request::root()}}/customer">Customers</a></li>
                        <li class="breadcrumb-item active">Import Customers</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.alert')
<div class="container-fluid select2-drpdwn">
    <div class="row">
        <div class="col-sm-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5>Import Customers</h5>
                </div>
                <form action="{{Request::root()}}/customer/import-data" method="POST" id="user-form" enctype="multipart/form-data">
                    @csrf
                <div class="card-body">
                
                    <a href="{{Request::root()}}/customer/sample-data">Sample File Download</a>
                    <div class="row">
                        
                        <div class="col-lg-6">
                            <div class="mb-2">
                                <div class="col-form-label">Import File <span class="red-star">*</span></div>
                                <input type="file" class="form-control required_field" name="import_file" value="" />
                                <input type="text" name="logo" id="cropped_val" hidden>
                            </div>
                           
                            @if ($errors->has('logo'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('logo') }}</strong>
                                </span>
                            @endif
                        </div>
                        

                    </div>
        
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-pill btn-success btn-air-success btn-air-success" data-original-title="" title=""><span>Submit</span>
                        </button>

                        <a href="{{Request::root()}}/customer">
                            <button type="button" class="btn btn-pill btn-danger btn-air-danger active"><span>Cancel</span>
                            </button>
                        </a>
                    </div>
                </div>
                </form>
                @if(Session::has('failoverwritelist'))
                    <div class="card-body">
                        <div class="file_import_error">
                                <?php $errors = Session::get('failoverwritelist'); ?>

                                @if(count($errors)>0)
                                    <table class="table display table-bordered table-striped table-hover">
                                        <tbody>
                                            <tr>
                                                <th class="center">LINE NO</th>
                                                <th class="center">REASON</th>
                                            </tr>
                                            @foreach ($errors as $key => $error)
                                            <tr>
                                                <td>{{ $error['line_no'] }}</td>
                                                <td>{{ $error['reason'] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                        </div>
                    <div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop