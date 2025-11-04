@extends('layouts.app')
@section('title','Video')
@section('breadcrumb')
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-sm-6"><h5 class="mb-2">Video</h5></div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Video</li>
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
                    <h5 class="mb-0">Data Video</h5>
                    <a href="{{ route('video.create') }}" class="btn btn-success btn-sm ms-auto">
                        <i class="fas fa-plus-square"></i> Create New
                    </a>
                </div>
                <div class="card-body">
                    <table id="tb_data" class="table table-bordered table-striped dt-responsive nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th width='5%'>No</th>
                                <th>Judul</th>
                                <th>Publish</th>
                                <th>Deskripsi</th>
                                <th width='5%'>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>  
        </div>
    </div>    
</div>
<!-- Modal View YouTube -->
<div class="modal fade" id="modalViewVideo" tabindex="-1" aria-labelledby="modalViewVideoLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalViewVideoLabel">View Video</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <h5 id="videoTitle" class="mb-3"></h5>
        <div class="ratio ratio-16x9">
            <iframe id="videoIframe" src="" title="YouTube video player" 
                frameborder="0" allow="accelerometer; autoplay; clipboard-write; 
                encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
            </iframe>
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
// Tombol View Video (YouTube)
$(document).on('click', '.btn-view', function() {
    const title = $(this).data('judul');
    const url = $(this).data('url');
    
    // Ubah URL YouTube menjadi format embed
    const embedUrl = convertYouTubeUrl(url);

    $('#videoTitle').text(title);
    $('#videoIframe').attr('src', embedUrl + '?autoplay=1');
    $('#modalViewVideo').modal('show');
});

// Hapus video saat modal ditutup
$('#modalViewVideo').on('hidden.bs.modal', function () {
    $('#videoIframe').attr('src', '');
});

// Fungsi konversi URL ke format embed
function convertYouTubeUrl(url) {
    // https://www.youtube.com/watch?v=abcd1234
    // https://youtu.be/abcd1234
    let videoId = '';
    if (url.includes('watch?v=')) {
        videoId = url.split('watch?v=')[1].split('&')[0];
    } else if (url.includes('youtu.be/')) {
        videoId = url.split('youtu.be/')[1].split('?')[0];
    }
    return 'https://www.youtube.com/embed/' + videoId;
}

$(document).on('click', '.btn-delete', function() {
    var id = $(this).data('id');

    Swal.fire({
        title: 'Yakin hapus data ini?',
        text: "Data yang sudah dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('video.destroy') }}",
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(res) {
                    Swal.fire({
                        icon: res.status,
                        title: res.messages,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('#tb_data').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menghapus data'
                    });
                }
            });
        }
    });
});

load_data = function(){
    $('#tb_data').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('video') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'judul', name: 'judul' },
            { data: 'publish', name: 'publish', orderable: false, searchable: false },
            { data: 'deskripsi', name: 'deskripsi' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
     
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

