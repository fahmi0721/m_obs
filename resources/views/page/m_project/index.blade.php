@extends('layouts.app')
@section('title','Project')
@section('breadcrumb')
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-sm-6"><h5 class="mb-2">Project</h5></div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Project</li>
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
                    <h5 class="mb-0">Data Project</h5>
                    <div class="ms-auto">
                        <a href="{{ route('project.export') }}" class="btn btn-primary btn-sm ms-auto">
                            <i class="fas fa-file-excel"></i> Export
                        </a>
                        <a href="{{ route('project.create') }}" class="btn btn-success btn-sm ms-auto">
                            <i class="fas fa-plus-square"></i> Upload
                        </a>
                        </a>
                    </div>
                    
                </div>
                <div class="card-body">
                    <table id="tb_data" class="table table-bordered table-striped dt-responsive nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th width='5%'>No</th>
                                <th>Project Code</th>
                                <th>Deskripsi</th>
                                <th>Entitas</th>
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
        ajax: "{{ route('project') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false,searchable:false },
            { data: 'project_code', name: 'm_project.project_code' },
            { data: 'deskripsi', name: 'm_project.deskripsi',orderable: false },
            { data: 'nama', name: 'm_entitas.nama' },
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

