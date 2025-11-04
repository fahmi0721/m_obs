@extends('layouts.app')
@section('title','Create Video')
@section('breadcrumb')
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-sm-6"><h5 class="mb-2">Upload Video</h5></div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('video') }}">Video</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create New Video</li>
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
        <div class="col-8">
            <div class="card card-success card-outline mb-4">
                <div class="card-header d-flex  align-items-center">
                    <h5 class="mb-0">Create Video</h5>
                </div>
                <!--end::Header-->
                <!--begin::Form-->
                <form action='javascript:void(0)' enctype="multipart/form-data" id="form_data">
                    @csrf
                    @method("post")
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="row mb-3">
                            <label for="judul" class="col-sm-3 col-form-label">Judul <span class='text-danger'>*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name='judul' placeholder='Judul' id="judul" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="deskripsi" class="col-sm-3 col-form-label">Deskripsi </label>
                            <div class="col-sm-9">
                                <textarea type="text" class="form-control" name='deskripsi' rows='3' placeholder='Deskripsi' id="deskripsi"></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="link" class="col-sm-3 col-form-label">Link YouTube <span class='text text-danger'>*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group mb-3">
                                    <input type="url" class="form-control" name='link' placeholder='Masukkan URL video YouTube' id="link" />
                                    <label class="input-group-text" for="link"><i class='fab fa-youtube'></i></label>
                                </div>
                                <small class="text-muted">Contoh: https://www.youtube.com/watch?v=dQw4w9WgXcQ</small>
                            </div>
                            <div id="preview" class="mt-3"></div>
                        </div>
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div class="card-footer">
                      <a href="{{ route('unit') }}" class="btn btn-danger btn-flat btn-sm"><i class="fa fa-mail-reply"></i> Kembali</a>
                      <button type="submit" id="btn-submit" class="btn btn-success btn-flat btn-sm float-end"><i class="fa fa-save"></i> Simpan</button>
                  </div>
                    <!--end::Footer-->
                </form>
                <!--end::Form-->
            </div>  
        </div>
    </div>    
</div>

@endsection
@section('js')
<script>
    $("#link").on("input", function() {
        let url = $(this).val();
        let videoId = url.split("v=")[1];
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
            url     : "{{ route('video.save') }}",
            data    : iData,
            cache   : false,
            processData: false,
            contentType: false,
            beforeSend  : function (){
                $("#btn-submit").html("<i class='fa fa-spinner fa-spin'></i>  Simpan..")
                $("#btn-submit").prop("disabled",true);
            },
            success: function(result){
                console.log(result)
                if(result.status == "success"){
                    position = "bottom-left";
                    icons = result.status;
                    pesan = result.messages;
                    title = "Saved!";
                    info(title,pesan,icons,position);
                    $("#btn-submit").html("<i class='fa fa-save'></i> Simpan")
                    $("#btn-submit").prop("disabled",false);
                    setTimeout(() => {
                        window.location.href = "{{ route('video') }}";
                    }, 2000);
                    
                }else{
                    error_message(result,'Proses Data Error');
                
                    $("#btn-submit").html("<i class='fa fa-save'></i> Simpan")
                    $("#btn-submit").prop("disabled",false);
                }
            },
            error: function(e){
                console.log(e)
                $("#btn-submit").html("<i class='fa fa-save'></i> Simpan")
                $("#btn-submit").prop("disabled",false);
                error_message(e,'Proses Data Error');
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

