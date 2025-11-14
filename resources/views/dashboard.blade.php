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
    <div class="row">
        <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
            <h5 class="card-title">Filter Area Kerja</h5>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                    <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                    <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                </button>
            </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select id="filterEntitas" class="form-control entitas">
                            <option value="">-- Semua Entitas --</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select id="filterRegional" class="form-control regional">
                            <option value="">-- Semua Regional --</option>
                        </select>
                    </div>

                    
                    <div class="col-md-4">
                        <select id="filterNrp" class="form-control nrp">
                            <option value="">-- Pilih Employee --</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button onclick="loadMap()" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
            <h5 class="card-title">MAP</h5>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                    <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                    <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                </button>
            </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div id="map" style="height: 600px;"></div>
            </div>
        </div>
        </div>
    </div>
     
</div>
@endsection
@section('js')
<script>
    $('.entitas').select2({
        ajax: {
            url: '{{ route("select.entitas") }}',
            dataType: 'json',
            delay: 250,
             data: function (params) {
                return {
                    q: params.term, 
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function(q){
                        return {id: q.id, text: q.nama};
                    })
                };
            },
            cache: true
        },
        theme: 'bootstrap4',
        width: '100%',
        placeholder: "-- Pilih Entitas --",
        allowClear: true
    });

    $('.nrp').select2({
        ajax: {
            url: '{{ route("select.nrp") }}',
            dataType: 'json',
            delay: 250,
             data: function (params) {
                return {
                    q: params.term, 
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function(q){
                        return {id: q.nrp, text: q.nrp+" - "+q.nama};
                    })
                };
            },
            cache: true
        },
        theme: 'bootstrap4',
        width: '100%',
        placeholder: "-- Pilih Employee --",
        allowClear: true
    });


    $('.regional').select2({
        ajax: {
            url: '{{ route("select.regional") }}',
            dataType: 'json',
            delay: 250,
             data: function (params) {
                return {
                    q: params.term, 
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function(q){
                        return {id: q.regional_id_tanos, text: q.nama};
                    })
                };
            },
            cache: true
        },
        theme: 'bootstrap4',
        width: '100%',
        placeholder: "-- Pilih Regional --",
        allowClear: true
    });
    const map = L.map('map').setView([-2.5, 118], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    async function loadMap() {
        // Buat icon custom
        const iconBiru = L.icon({
            iconUrl: '/icons/ship.png',
            iconSize: [32, 32], // Sesuaikan ukuran
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });

        const iconMerah = L.icon({
            iconUrl: '/icons/ship_red.png',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });
        let entitas  = document.getElementById('filterEntitas').value;
        let regional = document.getElementById('filterRegional').value;
        let nrp      = document.getElementById('filterNrp').value;
        let res = await fetch(`/api/map-data?entitas=${entitas}&regional=${regional}&nrp=${nrp}`);
        let data = await res.json();

        map.eachLayer(l => {
            if (l instanceof L.Marker) map.removeLayer(l)
        })
        let markers = L.featureGroup(); // ✅ untuk menampung marker
        data.forEach(r => {
            let popupHTML = `
                <div style="width:300px; max-height:250px; overflow:auto; font-size:13px;">
                    <b>${r.regional}</b>
                    <hr style="margin:5px 0"/>
            `;

            r.units.forEach(u => {
                popupHTML += `
                    <b>⚓ ${u.unit} (${u.unit_type})</b><br/>
                    <ul style="padding-left:15px;margin-bottom:8px;">`;
                u.crew.forEach(c => {
                    popupHTML += `<li>${c.nrp} - ${c.nama} <br/><small>(${c.job})</small></li>`;
                });
                popupHTML += `</ul><hr style="margin:5px 0"/>`;
            });

            popupHTML += `</div>`;

                    // Cek ada unit_type "kapal tunda" atau tidak dalam regional itu
            let isKapalTunda = r.units.some(u => u.unit_type.toLowerCase() === "kapal tunda");

            // Pilih icon
            let selectedIcon = isKapalTunda ? iconBiru : iconMerah;

            let marker = L.marker([r.lat, r.lng], { icon: selectedIcon })
                .bindPopup(popupHTML);
             marker.addTo(map);
            markers.addLayer(marker); // ✅ kumpulkan marker
        })
        // ✅ Auto zoom jika ada marker
        if (markers.getLayers().length > 0) {
            map.fitBounds(markers.getBounds().pad(0.3));
        } else {
            // alert("Data tidak ditemukan di lokasi manapun!");
        }
    }

    loadMap();
</script>    
@endsection
