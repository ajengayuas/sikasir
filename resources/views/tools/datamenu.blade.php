@extends('admin.admin')

@section('title')
Manage Menu
@endsection

@section('content_header')
Data Menu
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @can('menu-create')
                <button type="button" class="btn btn-primary" id="btntambah">
                    Tambah
                </button>
                @endcan
                <br><br>
                <table class="table table-hover table-bordered table-stripped" id="datatables">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Alias</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="addmenu" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="menuform" name="menuform" class="form-horizontal" method="post">
                    @csrf
                    <input type="hidden" id="id">
                    <table width="500" align="center">
                        <tr>
                            <td style="padding-left:20px">
                                <label>Nama Menu</label>
                            </td>
                            <td style="padding-left:20px">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="mnname" placeholder="Nama Menu" name="mnname" style="width:250px;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:20px">
                                <label>Alias</label>
                            </td>
                            <td style="padding-left:20px">
                                <input type="text" class="form-control" id="alias" placeholder="Alias" name="alias" style="width:250px;">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnsimpan">Simpan</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Keluar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('datamenu') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'mnname',
                    name: 'mnname'
                },
                {
                    data: 'alias',
                    name: 'alias'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('#btntambah').click(function(e) {
            $('#mnname').val('');
            $('#alias').val('');
            $('#exampleModalLabel').text('Tambah Menu');
            $('#addmenu').modal({
                show: true,
                backdrop: 'static'
            });
        });

        $('body').on('click', '#btnedit', function() {
            let id = $(this).attr('data-id');
            $.ajax({
                type: "post",
                url: "{!! route('editmenu') !!}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    let data = response.data;
                    $('#id').val(data.id);
                    $('#mnname').val(data.mnname);
                    $('#alias').val(data.alias);
                    $('#exampleModalLabel').text('Update Menu');
                    $('#addmenu').modal({
                        show: true,
                        backdrop: 'static'
                    });
                }
            });
        });

        $('#btnsimpan').click(function(e) {
            $('#btnsimpan').html('<i class="fas fa-hourglass"></i> Please Wait')
            $('#btnsimpan').prop('disabled', true);
            let id = $('#id').val();
            let mnname = $('#mnname').val();
            let alias = $('#alias').val();
            let aktif = 1
            if (mnname == '' || mnname == null) {
                $('#btnsimpan').html('Simpan')
                $('#btnsimpan').prop('disabled', false);
                notifalert('Nama');
            } else if (alias == '' || alias == null) {
                $('#btnsimpan').html('Simpan')
                $('#btnsimpan').prop('disabled', false);
                notifalert('Alias');
            } else {
                $.ajax({
                    type: "post",
                    url: (id == null || id == '') ? "{!! route('storemenu') !!}" : "{!! route('updatemenu') !!}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'id': id,
                        'mnname': mnname,
                        'alias': alias,
                        'aktif': aktif
                    },
                    dataType: "json",
                    success: function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: (response.status == 'error') ? 'error' : 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then((result) => {
                            if (response.status == 'success') {
                                $('#menuform').trigger("reset");
                                $('#addmenu').modal('hide');
                                $('#btnsimpan').html('Simpan');
                                $('#btnsimpan').prop('disabled', false);
                                $("#datatables").DataTable().ajax.reload(null, false);
                            } else {
                                $('#btnsimpan').html('Simpan');
                                $('#btnsimpan').prop('disabled', false);
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        $('#btnsimpan').html('Simpan');
                        $('#btnsimpan').prop('disabled', false);
                        Swal.fire({
                            title: 'Data Gagal Disimpan!',
                            text: 'Cek Data',
                            icon: 'error'
                        });
                        return;
                    }
                });
            }
        });

        $('body').on('click', '#btndelete', function(e) {
            let id = $(this).attr('data-id');
            Swal.fire({
                title: 'Perhatian',
                text: "Apakah Anda Yakin Menghapus Data Ini ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "{!! url('hapusmenu') !!}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id: id
                        },
                        dataType: "json",
                        success: function(response) {
                            Swal.fire({
                                title: response.title,
                                icon: (response.status == 'error') ? 'error' : 'success',
                                text: response.message,
                            }).then((result) => {
                                if (response.status == 'success') {
                                    $("#datatables").DataTable().ajax.reload(null, false);
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                title: 'Data Gagal Disimpan!',
                                text: 'Cek Data',
                                icon: 'error'
                            });
                            return;
                        }
                    })
                }
            });
        });

        function notifalert(params) {
            Swal.fire({
                title: 'Informasi',
                text: params + ' Tidak Boleh Kosong',
                icon: 'warning'
            });
            return;
        }
    });
</script>
@endsection