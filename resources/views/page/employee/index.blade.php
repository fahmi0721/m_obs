@extends('layouts.app')
@section('title','Employee')
@section('breadcrumb')
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-sm-6"><h5 class="mb-2">Employee</h5></div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Employee</li>
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
                    <h5 class="mb-0">Data Employee</h5>
                    <a href="{{ route('employee.create') }}" class="btn btn-success btn-sm ms-auto">
                        <i class="fas fa-plus-square"></i> Upload
                    </a>
                </div>
                <div class="card-body">
                    <table id="tb_data" class="table table-bordered table-striped dt-responsive nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th width='5%'>No</th>
                                <th>Employee ID Tanos</th>
                                <th>NRP</th>
                                <th>Nama</th>
                                <th>Usia</th>
                                <th>Gender</th>
                                <th>Status</th>
                                <th>Religion</th>
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
        ajax: "{{ route('employee') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'employee_id_tanos', name: 'employee_id_tanos' },
            { data: 'nrp', name: 'nrp' },
            { data: 'nama', name: 'nama' },
            { data: 'umur', name: 'umur',orderable: false, searchable: false },
            { data: 'gender', name: 'gender',orderable: false, searchable: false },
            { data: 'status', name: 'status',searchable: false },
            { data: 'religion', name: 'religion',searchable: false },
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

