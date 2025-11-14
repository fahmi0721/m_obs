@extends('layouts.app')
@section('title','Create Surat Edaran')
@section('breadcrumb')
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-sm-6"><h5 class="mb-2">Upload Surat Edaran</h5></div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('edaran') }}">Surat Edaran</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create New Surat Edaran</li>
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
                    <h5 class="mb-0">Create Surat Edaran</h5>
                </div>
                <!--end::Header-->
                <!--begin::Form-->
                <form action='javascript:void(0)' enctype="multipart/form-data" id="form_data">
                    @csrf
                    @method("post")
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="row mb-3">
                            <label for="no_surat" class="col-sm-3 col-form-label">No Surat <span class='text-danger'>*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name='no_surat' placeholder='No Surat' id="no_surat" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="perihal" class="col-sm-3 col-form-label">Perihal <span class='text-danger'>*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name='perihal' placeholder='Perihal' id="perihal" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="tgl_surat" class="col-sm-3 col-form-label">Tanggal Surat <span class='text-danger'>*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name='tgl_surat' placeholder='Tanggal Surat' id="tgl_surat" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="file" class="col-sm-3 col-form-label">File <span class='text text-danger'>*</span></label>
                                <div class="col-sm-9">
                                    <div class="input-group mb-3">
                                    <input type="file" class="form-control" accept='pdf' name='file' placeholder='File SOP' id="file" />
                                    <label class="input-group-text" for="logo"><i class='fa fa-file-pdf'></i></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div class="card-footer">
                      <a href="{{ route('edaran') }}" class="btn btn-danger btn-flat btn-sm"><i class="fa fa-mail-reply"></i> Kembali</a>
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
    $(document).ready(function(){
        flatpickr("#tgl_surat", {
            altInput: true,
            altFormat: "d F Y",   // tampilan di input: 10 Juli 2025
            dateFormat: "Y-m-d",  // format yang dikirim ke backend: 2025-07-10
            allowInput: false,
            locale: "id"
        });
    });
    proses_data = function(){
        let iData = new FormData(document.getElementById("form_data"));
        $.ajax({
            type    : "POST",
            url     : "{{ route('edaran.save') }}",
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
                        window.location.href = "{{ route('edaran') }}";
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

