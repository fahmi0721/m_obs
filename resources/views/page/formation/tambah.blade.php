@extends('layouts.app')
@section('title','Upload Formasi')
@section('breadcrumb')
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-sm-6"><h5 class="mb-2">Upload Formasi</h5></div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('formation') }}">Formasi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Upload</li>
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
                    <h5 class="mb-0">Upload Formation</h5>
                    <a href="{{ route('formation.template') }}" class="btn btn-primary btn-sm ms-auto">
                        <i class="fas fa-download"></i> Download Template
                    </a>
                </div>
                <!--end::Header-->
                <!--begin::Form-->
                <form action='javascript:void(0)' enctype="multipart/form-data" id="form_data">
                    @csrf
                    @method("post")
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="row mb-3">
                        <label for="file" class="col-sm-3 col-form-label">File</label>
                        <div class="col-sm-9">
                            <div class="input-group mb-3">
                                <input type="file" class="form-control" accept='.xlsx,.xls,.csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv"' name='file' placeholder='File' id="file" />
                                <label class="input-group-text" for="logo">Upload</label>
                            </div>
                        </div>
                        </div>
                        
                        
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div class="card-footer">
                      <a href="{{ route('formation') }}" class="btn btn-danger btn-flat btn-sm"><i class="fa fa-mail-reply"></i> Kembali</a>
                      <button type="submit" id="btn-submit" class="btn btn-success btn-flat btn-sm float-end"><i class="fa fa-save"></i> Simpan</button>
                  </div>
                    <!--end::Footer-->
                </form>
                <!--end::Form-->
            </div>  
        </div>
    </div>    
</div>

<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Terjadi Kesalahan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="errorModalBody">
        <!-- isi error nanti diisi dari JavaScript -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection
@section('js')
<script>
    proses_data = function(){
        let iData = new FormData(document.getElementById("form_data"));
        $.ajax({
            type    : "POST",
            url     : "{{ route('formation.save') }}",
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
                        window.location.href = "{{ route('formation') }}";
                    }, 2000);
                    
                }else if (result.status === 'error') {
                    let errorHtml = '';

                    result.errors.forEach(function (err) {
                        errorHtml += `<p class="text-danger mb-1">• ${err.message}</p>`;
                    });

                    $('#errorModalBody').html(errorHtml);
                    $('#errorModal').modal('show');
                    $("#btn-submit").html("<i class='fa fa-save'></i> Simpan")
                    $("#btn-submit").prop("disabled",false);
                }else if (result.status === 'errors') {
                    let errorHtml = '';

                    result.errors.forEach(function (err) {
                        errorHtml += `<p class="text-danger mb-1">• ${err.message}</p>`;
                    });

                    $('#errorModalBody').html(errorHtml);
                    $('#errorModal').modal('show');
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

