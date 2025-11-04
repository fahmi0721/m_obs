@extends('layouts.app')
@section('title','Edit SOP')
@section('breadcrumb')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6"><h5 class="mb-2">Edit SOP</h5></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sop') }}">SOP</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit SOP</li>
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
                    <h5 class="mb-0">Edit SOP</h5>
                </div>

                <form action="javascript:void(0)" enctype="multipart/form-data" id="form_data">
                    @csrf
                    @method("PUT")

                    <input type="hidden" name="id" value="{{ $sop->id }}">

                    <div class="card-body">
                        <div class="row mb-3">
                            <label for="kode" class="col-sm-3 col-form-label">Kode <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="kode" value="{{ $sop->kode }}" id="kode" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="nama" class="col-sm-3 col-form-label">Nama <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama" value="{{ $sop->nama }}" id="nama" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="deskripsi" class="col-sm-3 col-form-label">Deskripsi</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="deskripsi" rows="3" id="deskripsi">{{ $sop->deskripsi }}</textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Status <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group mb-3">
                                    <label class="input-group-text">
                                        <input type="radio" name="status" value="valid" {{ $sop->status == 'valid' ? 'checked' : '' }}>
                                    </label>
                                    <input type="text" class="form-control" disabled value="Valid" />
                                    <label class="input-group-text">
                                        <input type="radio" name="status" value="invalid" {{ $sop->status == 'invalid' ? 'checked' : '' }}>
                                    </label>
                                    <input type="text" class="form-control" disabled value="Invalid" />
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="file" class="col-sm-3 col-form-label">File SOP</label>
                            <div class="col-sm-9">
                                @if($sop->file)
                                    <p class="mb-2">
                                        <a href="{{ asset('uploads/sop/'.$sop->file) }}" target="_blank" class="btn btn-outline-primary btn-sm">
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
                        <a href="{{ route('sop') }}" class="btn btn-danger btn-flat btn-sm">
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
    function proses_data(){
        let iData = new FormData(document.getElementById("form_data"));

        $.ajax({
            type    : "POST",
            url     : "{{ route('sop.update') }}", // route untuk update
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
                        window.location.href = "{{ route('sop') }}";
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
