@extends('layouts.app')
@section('title','Edit Surat Edaran')
@section('breadcrumb')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6"><h5 class="mb-2">Edit Surat Edaran</h5></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('edaran') }}">Surat Edaran</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Surat Edaran</li>
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
                    <h5 class="mb-0">Edit Surat Edaran</h5>
                </div>

                <form action="javascript:void(0)" enctype="multipart/form-data" id="form_data">
                    @csrf
                    @method("PUT")

                    <input type="hidden" name="id" value="{{ $sop->id }}">

                    <div class="card-body">
                        <div class="row mb-3">
                            <label for="no_surat" class="col-sm-3 col-form-label">No Surat <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="no_surat" value="{{ $sop->no_surat }}" id="no_surat" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="perihal" class="col-sm-3 col-form-label">Perihal <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="perihal" value="{{ $sop->perihal }}" id="perihal" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="tgl_surat" class="col-sm-3 col-form-label">Tanggal Surat <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="tgl_surat" value="{{ $sop->tanggal_surat }}" id="tgl_surat" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="file" class="col-sm-3 col-form-label">File SOP</label>
                            <div class="col-sm-9">
                                @if($sop->file)
                                    <p class="mb-2">
                                        <a href="{{ asset('uploads/edaran/'.$sop->file) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="fa fa-file-pdf"></i> Lihat File Lama
                                        </a>
                                    </p>
                                @endif
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" accept="application/pdf" name="file" id="file">
                                    <label class="input-group-text"><i class="fa fa-file-pdf"></i></label>
                                </div>
                                <small class="text-muted">Kosongkan jika tidak ingin mengganti file.</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('edaran') }}" class="btn btn-danger btn-flat btn-sm">
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
     $(document).ready(function(){
        flatpickr("#tgl_surat", {
            altInput: true,
            altFormat: "d F Y",   // tampilan di input: 10 Juli 2025
            dateFormat: "Y-m-d",  // format yang dikirim ke backend: 2025-07-10
            allowInput: false,
            locale: "id"
        });
    });
    function proses_data(){
        let iData = new FormData(document.getElementById("form_data"));

        $.ajax({
            type    : "POST",
            url     : "{{ route('edaran.update') }}", // route untuk update
            data    : iData,
            cache   : false,
            processData: false,
            contentType: false,
            beforeSend: function (){
                $("#btn-submit").html("<i class='fa fa-spinner fa-spin'></i>  Menyimpan..")
                $("#btn-submit").prop("disabled",true);
            },
            success: function(result){
                if(result.status == "success"){
                    info("Updated!", result.messages, "success", "bottom-left");
                    $("#btn-submit").html("<i class='fa fa-save'></i> Update")
                    $("#btn-submit").prop("disabled",false);
                    setTimeout(() => {
                        window.location.href = "{{ route('edaran') }}";
                    }, 2000);
                }else{
                    error_message(result, 'Proses Update Error');
                    $("#btn-submit").html("<i class='fa fa-save'></i> Update")
                    $("#btn-submit").prop("disabled",false);
                }
            },
            error: function(e){
                error_message(e, 'Proses Update Error');
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
