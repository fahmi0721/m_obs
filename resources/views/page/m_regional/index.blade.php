@extends('layouts.app')
@section('title','Regional')
@section('breadcrumb')
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-sm-6"><h5 class="mb-2">Regional</h5></div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Regional</li>
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
                    <h5 class="mb-0">Data Regional</h5>
                    <a href="{{ route('regional.create') }}" class="btn btn-success btn-sm ms-auto">
                        <i class="fas fa-plus-square"></i> Upload
                    </a>
                </div>
                <div class="card-body">
                    <table id="tb_data" class="table table-bordered table-striped dt-responsive nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th width='5%'>No</th>
                                <th>Regional ID Tanos</th>
                                <th>Regional</th>
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
        ajax: "{{ route('regional') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'regional_id_tanos', name: 'regional_id_tanos' },
            { data: 'nama', name: 'nama' },
        ],
        dom: '<"d-flex justify-content-between align-items-center mb-2"lBf>rtip',
        buttons: [
            { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-success btn-sm' },
            { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-danger btn-sm' },
            { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-secondary btn-sm' },
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

