@extends('layouts.app')
@section('title','Update SOP Jabatan')
@section('breadcrumb')
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-sm-6"><h5 class="mb-2">Upload SOP Jabatan</h5></div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sop_jabatan') }}">SOP Jabatan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Update New SOP Jabatan</li>
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
                    <h5 class="mb-0">Update SOP SOP Jabatan</h5>
                </div>
                <!--end::Header-->
                <!--begin::Form-->
                <form action='javascript:void(0)' enctype="multipart/form-data" id="form_data">
                    @csrf
                    @method("put")
                    <input type="hidden" name='id' id='id' value='{{ $id }}'>
                    <!--begin::Body-->
                    <div class="card-body">

                        <div class="row mb-3">
                            <label for="nama" class="col-sm-3 col-form-label">Nama <span class='text-danger'>*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ $data->nama }}" name='nama' placeholder='Nama' id="nama" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="job_id_tanos" class="col-sm-3 col-form-label">Jabatan <span class='text-danger'>*</span></label>
                            <div class="col-sm-9">
                                <select name="job_id_tanos" id="job_id_tanos" class="form-control job_id_tanos">
                                    <option value="">-- Pilih Jabatan --</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="unit_type" class="col-sm-3 col-form-label">Tipe Unit <span class='text-danger'>*</span></label>
                            <div class="col-sm-9">
                                <select name="unit_type" id="unit_type" class="form-control unit_type">
                                    <option value="">-- Pilih Tipe Unit --</option>
                                </select>
                            </div>
                        </div>
                        

                        <div class="row mb-3">
                            <label for="file" class="col-sm-3 col-form-label">File SOP <span class='text text-danger'>*</span></label>
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
    $(document).ready(function(){
       
        $('.job_id_tanos').select2({
            ajax: {
                url: '{{ route("job.select") }}',
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: data.map(function(q){
                            return {id: q.job_id_tanos, text: q.nama};
                        })
                    };
                },
                cache: true
            },
            theme: 'bootstrap4',
            width: '100%',
            placeholder: "-- Pilih Jabatan --",
            allowClear: true
        });

        $('.unit_type').select2({
            ajax: {
                url: '{{ route("unit.select") }}',
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: data.map(function(q){
                            return {id: q.unit_type, text: q.unit_type};
                        })
                    };
                },
                cache: true
            },
            theme: 'bootstrap4',
            width: '100%',
            placeholder: "-- Pilih Jabatan --",
            allowClear: true
        });

        @if(!empty($jabatan))
            var option = new Option("{{ $jabatan->nama }}", {{ $jabatan->job_id_tanos }}, true, true);
            $(".job_id_tanos").append(option).trigger('change');    
        @endif

        @if(!empty($unit_type))
            var option = new Option("{{ $unit_type->unit_type }}", "{{ $unit_type->unit_type }}", true, true);
            $(".unit_type").append(option).trigger('change');    
        @endif
      
    })
    proses_data = function(){
        let iData = new FormData(document.getElementById("form_data"));
        $.ajax({
            type    : "POST",
            url     : "{{ route('sop_jabatan.update') }}",
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
                        window.location.href = "{{ route('sop_jabatan') }}";
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

