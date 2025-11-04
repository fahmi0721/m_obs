@extends('layouts.app')
@section('title','Users')
@section('breadcrumb')
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-sm-6"><h5 class="mb-2">Users</h5></div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Users</li>
        </ol>
        </div>
    </div>
    <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-success card-outline mb-4">
                <div class="card-header d-flex  align-items-center">
                    <h5 class="mb-0">Data Users</h5>
                    <!-- <a href="{{ route('employee.create') }}" class="btn btn-success btn-sm ms-auto">
                        <i class="fas fa-plus-square"></i> Upload
                    </a> -->
                </div>
                <div class="card-body">
                    <table id="tb_data" class="table table-bordered table-striped dt-responsive nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th width='5%'>No</th>
                                <th>Username</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Level</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>  
        </div>
    </div>    
</div>
@endsection
@section('js')
<script>
$(document).ready(function() {
    load_data();
});

load_data = function(){
    $('#tb_data').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('users') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'username', name: 'username' },
            { data: 'nama', name: 'nama' },
            { data: 'email', name: 'email' },
            { data: 'level', name: 'level',orderable: false, searchable: false },
        ]
    });
    // Init tooltip setiap setelah table redraw
    $('#tb_data').on('draw.dt', function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });

    // Init pertama kali
    $('[data-bs-toggle="tooltip"]').tooltip();
}
</script>
@endsection

