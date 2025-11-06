@extends('layouts.app')
@section('title','Dashboard')
@section('breadcrumb')
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-sm-6"><h5 class="mb-2">Management Onboarding System</h5></div>
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
<style>
    #map {
    width: 100% !important;
    height: 500px !important;
    display: block;
    border-radius: 10px;
    }

    .leaflet-container {
    background: #e0e0e0; /* warna dasar netral kalau map belum muncul */
    }

    .card-body {
    overflow: hidden !important; /* cegah scroll abu-abu di kanan */
    }
</style>
<div class="container-fluid">
    <!-- Small Box (Stat card) -->
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="info-box">
                <span class="info-box-icon text-bg-success shadow-sm">
                <i class="fa fa-user"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ $mydata->nama_karyawan }}</span>
                    <span class="info-box-number">{{ $mydata->nama_formation }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="info-box">
                <span class="info-box-icon text-bg-primary shadow-sm">
                <i class="fa fa-location-pin"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ $mydata->nama_unit }}</span>
                    <span class="info-box-number">{{ $mydata->nama_regional }}</span>
                </div>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-6">
            <div class="info-box">
                <span class="info-box-icon text-bg-warning shadow-sm">
                <i class="fa fa-users"></i>
                </span>
                <div class="info-box-content">
                <span class="info-box-text">Jumlah Crew</span>
                <span class="info-box-number">{{ $countMyTeam }} Crew</span>
            </div>
        </div>
    </div>
    <!-- /.row -->
     <div class="row mb-4">
        <div class="col-lg-12 col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><b>Map Area Kerja</b></h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                        <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                        <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
                        <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body card-body p-0 m-0">
                    <div id="map" style="height: 500px; width: 100%; border-radius: 10px;"></div>
                </div>
                <!-- /.card-footer -->
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-12 mb-4">
            <div class="card">
                <div class="card-header">
                <h3 class="card-title"><b>Job Description</b></h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                    <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                    <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
                    <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    @if(!$sop_jabatan)
                         <div class="p-3 text-center">
                            <b>SOP untuk jabatan tidak ditemukan</b>
                        </div>
                    @else
                    <iframe 
                        src="{{ asset('uploads/sop_jabatan/'.$sop_jabatan->file) }}" 
                        width="100%" 
                        height="600px"
                        style="border:none;">
                    </iframe>
                    @endif
                <!-- /.table-responsive -->
                </div>
                
                <!-- /.card-footer -->
            </div>
        </div>


        <div class="col-lg-12 col-12 mb-4">
            <div class="card">
                <div class="card-header">
                <h3 class="card-title"><b>My Team</b></h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                    <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                    <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
                    <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                <div class="table-responsive">
                    <table class='table table-striped'>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NRP</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Usia</th>
                                <th>Agama</th>
                            </tr>
                        </thead>
                        @php $no=1; @endphp
                        @foreach($my_team as $team)
                            <tr>
                                <td>{{ $no }}</td>
                                <td>{{ $team->nrp }}</td>
                                <td>{{ $team->nama_karyawan }}</td>
                                <td>{{ $team->nama_formation }}</td>
                                <td>{{ $team->umur }}</td>
                                <td>{{ $team->religion }}</td>
                            </tr>
                        @php $no++ @endphp
                        @endforeach
                    </table>
                </div>
                <!-- /.table-responsive -->
                </div>
                
                <!-- /.card-footer -->
            </div>
        </div>
        <div class="col-lg-12 col-12 mb-4">
            <div class="card">
                <div class="card-header">
                <h3 class="card-title"><b>Daftar SOP</b></h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                    <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                    <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
                    <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                <div class="table-responsive">
                    <table class='table table-striped'>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor</th>
                                <th>Nama</th>
                                <th>File SOP</th>
                            </tr>
                        </thead>
                        @php $no=1; @endphp
                        @foreach($sop as $sp)
                            <tr>
                                <td>{{ $no }}</td>
                                <td>{{ $sp->kode }}</td>
                                <td>{{ $sp->nama }}</td>
                                <td><button class="btn btn-info btn-sm btn-view" data-id="{{ $sp->id }}"  data-url="{{ asset('uploads/sop/' . $sp->file) }}" data-bs-toggle="tooltip" title="Lihat SOP"><i class="fas fa-eye"></i></button></td>
                            </tr>
                        @php $no++ @endphp
                        @endforeach
                    </table>
                </div>
                <!-- /.table-responsive -->
                </div>
                <!-- /.card-footer -->
            </div>
        </div>

        <div class="col-lg-12 col-12">
            <div class="card">
                <div class="card-header">
                <h3 class="card-title"><b>Video</b></h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                    <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                    <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
                    <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                <div class="table-responsive">
                    <table class='table table-striped'>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Video</th>
                            </tr>
                        </thead>
                        @php $no=1; @endphp
                        @foreach($videos as $video)
                            <tr>
                                <td>{{ $no }}</td>
                                <td>{{ $video->judul }}</td>
                                <td>{{ $video->deskripsi }}</td>
                                <td><button class="btn btn-info btn-sm btn-view-video" data-id="{{ $video->id }}" data-url="{{ $video->link }}"   data-bs-toggle="tooltip" title="Lihat Video"><i class="fas fa-video"></i></button></td>
                            </tr>
                        @php $no++ @endphp
                        @endforeach
                    </table>
                </div>
                <!-- /.table-responsive -->
                </div>
                
                <!-- /.card-footer -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview SOP -->
<div class="modal fade" id="modalViewSop" tabindex="-1" aria-labelledby="modalViewSopLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalViewSopLabel">Lihat SOP</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="height: 80vh;">
        <iframe id="sopViewer" src="" width="100%" height="100%" style="border: none;"></iframe>
      </div>
    </div>
  </div>
</div>

<!-- Modal Preview Video -->
<div class="modal fade" id="modalViewVideo" tabindex="-1" aria-labelledby="modalViewVideoLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalViewVideoLabel">Lihat Video</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="height: 80vh; background-color: #000;">
        <iframe id="youtubeViewer" width="100%" height="100%" frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen style="border:none;"></iframe>
      </div>
    </div>
  </div>
</div>

@endsection
@section('js')
<script>
    
    document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi peta
    var map = L.map('map').setView([-2.5, 118], 5); // Fokus ke Indonesia
    window.addEventListener('resize', () => {
        map.invalidateSize();
    });
    // Tambahkan layer peta (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Definisikan ikon kapal
    var shipIcon = L.icon({
        iconUrl: '/icons/ship.png', // pastikan file ini ada di public/icons/
        iconSize: [40, 40],         // ukuran ikon
        iconAnchor: [20, 40],       // titik tengah bawah ikon
        popupAnchor: [0, -35]       // posisi popup relatif terhadap ikon
    });

    // Definisikan ikon kapal
    var shipIconRed = L.icon({
        iconUrl: '/icons/ship_red.png', // pastikan file ini ada di public/icons/
        iconSize: [40, 40],         // ukuran ikon
        iconAnchor: [20, 40],       // titik tengah bawah ikon
        popupAnchor: [0, -35]       // posisi popup relatif terhadap ikon
    });
    // Ambil data pelabuhan
    fetch("{{ route('api.regional') }}")
        .then(response => response.json())
        .then(data => {
             let loginUserCoords = null;
            data.forEach(pelabuhan => {
                if(pelabuhan.latitude && pelabuhan.longitude){
                    // Tentukan apakah ini regional milik user login
                    let isMyRegional = pelabuhan.is_login_user === true;

                    // Pilih ikon berdasarkan status
                    let iconToUse = isMyRegional ? shipIconRed : shipIcon;

                    let marker = L.marker([pelabuhan.latitude, pelabuhan.longitude],{ icon: iconToUse }).addTo(map);
                    marker.bindPopup(`
                        <strong>${pelabuhan.nama}</strong><br>
                    `);
                     // Simpan koordinat user login untuk zoom nanti
                    if (isMyRegional) {
                        loginUserCoords = [pelabuhan.latitude, pelabuhan.longitude];
                    }
                }
            });
            // Jika ditemukan regional user login, fokuskan peta ke situ
            if (loginUserCoords) {
                map.setView(loginUserCoords, 10); // zoom level 10, bisa disesuaikan
            } else {
                map.setView([-2.5, 118], 5); // fallback ke peta Indonesia
            }
        })
        .catch(error => console.error('Error:', error));
    });
    $(document).ready(function() {
         $('[data-bs-toggle="tooltip"]').tooltip();
         // Event tombol "Lihat SOP"
        $(document).on('click', '.btn-view', function() {
            let fileUrl = $(this).data('url');
            $("#sopViewer").attr("src", fileUrl);
            $("#modalViewSop").modal('show');
        });

        // Hapus src saat modal ditutup (biar tidak terus load)
        $('#modalViewSop').on('hidden.bs.modal', function () {
            $("#sopViewer").attr("src", "");
        });
    });
     // Init pertama kali

     // Event tombol "Lihat Video"
    $(document).on('click', '.btn-view-video', function() {
        let youtubeUrl = $(this).data('url');

        // Konversi URL YouTube biasa → URL embed
        let embedUrl = youtubeUrl
            .replace('watch?v=', 'embed/')
            .replace('&t=', '?start=');

        $("#youtubeViewer").attr("src", embedUrl + "?autoplay=1");
        $("#modalViewVideo").modal('show');
    });

    // Bersihkan iframe saat modal ditutup
    $('#modalViewVideo').on('hidden.bs.modal', function () {
        $("#youtubeViewer").attr("src", "");
    });
   
</script>


@endsection