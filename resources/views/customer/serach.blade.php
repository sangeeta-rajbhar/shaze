<div class="container-fluid select2-drpdwn">
    <div class="row">
        <div class="col-sm-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5>Filter</h5>
                </div>
                <form action="{{Request::root()}}/customer" method="GET" id="user-form" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="mb-2">
                                    <div class="col-form-label">Name</div>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Name" autocomplete="off" value="{{$_GET['name'] ?? ''}}"/>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-2">
                                    <div class="col-form-label">Email</div>
                                    <input type="text" class="form-control" name="email" id="email" placeholder="Email" autocomplete="off" value="{{$_GET['email'] ?? ''}}" />
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="mb-2">
                                    <div class="col-form-label">Created Date  (Range)</div>
                                    <input class="form-control digits" type="text" name="daterange" value="{{$_GET['daterange'] ?? ''}}">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer-ss">
                            <button type="submit" class="btn btn-pill btn-success btn-air-success btn-air-success" name="action" value="search"><span>Search</span>
                            </button>

                            <a href="{{Request::root()}}/customer">
                                <button type="button" class="btn btn-pill btn-danger btn-air-danger active"><span>Reset</span>
                                </button>
                            </a>

                            <button type="submit" class="btn btn-pill btn-warning btn-air-warning btn-air-warning" name="action" value="export"><span>Export</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>