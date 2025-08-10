@if(Session::has('message'))
<div class="padding">
    <div class="alert alert-success dark alert-dismissible fade show" role="alert"><i class="icon-thumb-up"></i> <strong>Well done ! </strong>{{ Session::get('message') }}.
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif
@if(Session::has('alert'))
<div class="padding">
    <div class="alert alert-danger dark alert-dismissible fade show" role="alert"><i class="icon-thumb-down"></i> <strong>Oops ! </strong>{{ Session::get('alert') }}.
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif