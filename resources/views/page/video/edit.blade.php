@extends('layouts.app')
@section('title','Edit Video')
@section('breadcrumb')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6"><h5 class="mb-2">Edit Video</h5></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('video') }}">Video</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Video</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-8">
            <div class="card card-success card-outline mb-4">
                <div class="card-header d-flex align-items-center">
                    <h5 class="mb-0">Edit Video</h5>
                </div>

                <form action="javascript:void(0)" enctype="multipart/form-data" id="form_data">
                    @csrf
                    @method("PUT")

                    <div class="card-body">
                        <input type="hidden" name="id" value="{{ $video->id }}">

                        <div class="row mb-3">
                            <label for="judul" class="col-sm-3 col-form-label">Judul <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="judul" id="judul" value="{{ $video->judul }}" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="deskripsi" class="col-sm-3 col-form-label">Deskripsi</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="deskripsi" id="deskripsi" rows="3">{{ $video->deskripsi }}</textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="link" class="col-sm-3 col-form-label">Link YouTube <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group mb-3">
                                    <input type="url" class="form-control" name="link" id="link"
                                        placeholder="Masukkan URL video YouTube"
                                        value="{{ $video->link }}" />
                                    <label class="input-group-text" for="link"><i class="fab fa-youtube"></i></label>
                                </div>
                                <small class="text-muted">Contoh: https://www.youtube.com/watch?v=dQw4w9WgXcQ</small>

                                <div id="preview" class="mt-3">
                                    @php
                                        $videoId = '';
                                        if (str_contains($video->link, 'v=')) {
                                            $videoId = explode('v=', $video->link)[1];
                                            if (str_contains($videoId, '&')) {
                                                $videoId = explode('&', $videoId)[0];
                                            }
                                        }
                                    @endphp
                                    @if($videoId)
                                        <iframe width="100%" height="315" src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allowfullscreen></iframe>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('video') }}" class="btn btn-danger btn-flat btn-sm">
                            <i class="fa fa-mail-reply"></i> Kembali
                        </a>
                        <button type="submit" id="btn-submit" class="btn btn-success btn-flat btn-sm float-end">
                            <i class="fa fa-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>  
        </div>
    </div>    
</div>
@endsection

@section('js')
<script>
$("#link").on("input", function() {
    let url = $(this).val();
    let videoId = "";
    if (url.includes("v=")) {
        videoId = url.split("v=")[1];
        if (videoId.includes("&")) {
            videoId = videoId.split("&")[0];
        }
    } else if (url.includes("youtu.be/")) {
        videoId = url.split("youtu.be/")[1];
    }

    if (videoId) {
        $("#preview").html(`<iframe width="100%" height="315" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe>`);
    } else {
        $("#preview").html("");
    }
});

proses_data = function(){
    let iData = new FormData(document.getElementById("form_data"));
    $.ajax({
        type    : "POST",
        url     : "{{ route('video.update', $video->id) }}",
        data    : iData,
        cache   : false,
        processData: false,
        contentType: false,
        beforeSend  : function (){
            $("#btn-submit").html("<i class='fa fa-spinner fa-spin'></i>  Update..")
            $("#btn-submit").prop("disabled",true);
        },
        success: function(result){
            if(result.status == "success"){
                info("Updated!", result.messages, result.status, "bottom-left");
                $("#btn-submit").html("<i class='fa fa-save'></i> Update")
                $("#btn-submit").prop("disabled",false);
                setTimeout(() => {
                    window.location.href = "{{ route('video') }}";
                }, 1500);
            }else{
                error_message(result, 'Proses Data Error');
                $("#btn-submit").html("<i class='fa fa-save'></i> Update")
                $("#btn-submit").prop("disabled",false);
            }
        },
        error: function(e){
            error_message(e, 'Proses Data Error');
            $("#btn-submit").html("<i class='fa fa-save'></i> Update")
            $("#btn-submit").prop("disabled",false);
        }
    })
}

$(function() {
    $("#form_data").submit(function(e){
        e.preventDefault();
        proses_data();
    });
});
</script>
@endsection
