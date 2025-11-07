@extends('layouts.app')
@section('title','Dashboard')
@section('breadcrumb')
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-sm-6"><h5 class="mb-2">Dashboard</h5></div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
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
        <div class="col-lg-4 col-6">
            <div class="info-box">
                <span class="info-box-icon text-bg-success shadow-sm">
                <i class="fa fa-users"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Employe</span>
                    <span class="info-box-number">{{ number_format($dashboard->total_employee,0,',','.') }} Crew</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="info-box">
                <span class="info-box-icon text-bg-primary shadow-sm">
                <i class="fa fa-box"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Unit/Kapal</span>
                    <span class="info-box-number">{{ $dashboard->total_unit }} Kapal</span>
                </div>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-6">
            <div class="info-box">
                <span class="info-box-icon text-bg-warning shadow-sm">
                <i class="fa fa-box"></i>
                </span>
                <div class="info-box-content">
                <span class="info-box-text">Total Entitas</span>
                <span class="info-box-number">{{ $dashboard->total_entitas }} Entitas</span>
            </div>
        </div>
    </div>
    <!-- /.row -->
     
</div>
@endsection
