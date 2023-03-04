@extends('admin.admin')

@section('title')
Manage Role
@endsection

@section('content_header')
Data Group Access
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @can('role-create')
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
<div class="modal fade" id="addrole" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="roleform" name="roleform" class="form-horizontal" method="post">
                    @csrf
                    <input type="hidden" id="id">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control','id' => 'name')) !!}
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group">
                                <!-- <strong>Permission:</strong> -->
                                <br />
                                <table style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center">
                                                <input type="checkbox" name="checkall" id="checkall">
                                            </th>
                                            <th>
                                                <label>Permission</label>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($permission as $key => $value)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="mycheckbok" id="mycheckbok" value="{{$value->id}}">
                                            </td>
                                            <td>{{ $value->name }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnsimpan">Simpan</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Keluar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addshow" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="showform" name="showform" class="form-horizontal" method="post">
                    @csrf
                    <input type="hidden" id="id">
                    <table width="500" align="center">
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Name:</strong>
                                            <input type="text" class="form-control" id="nameshow" placeholder="Nama" name="nameshow" style="width:250px;" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Permission:</strong>
                                            <br />
                                            <div class="job-summ-panel" id="job-summ-panel">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Keluar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="editrole" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="editroleform" name="editroleform" class="form-horizontal" method="post">
                    @csrf
                    <input type="hidden" id="id">
                    <table width="500" align="center">
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Name:</strong>
                                            <input type="text" class="form-control" id="nameedit" placeholder="Nama" name="nameedit" style="width:250px;">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <table>
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" name="checkalledit" id="checkalledit">
                                                        &emsp;Permission
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="job-summ-panel" id="job-summ-panel2">

                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnupdate">Simpan</button>
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

        var username = @JSON($userlogin);

        $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('datarole') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'name',
                    name: 'name'
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
            $('#name').val('');
            $('#addrole').modal({
                show: true,
                backdrop: 'static'
            });
        });

        $('body').on('click', '#btnshow', function() {
            $('#addshow').modal({
                show: true,
                backdrop: 'static'
            });
            let id = $(this).attr('data-id');
            $.ajax({
                type: "get",
                url: "{!! route('showrole') !!}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    $('#id').val(response.role.id);
                    $('#nameshow').val(response.role.name);
                    var appendString = "";
                    for (var i = 0; i < response.rp.length; i++) {
                        appendString += "<div>" + response.rp[i].name + "</div>";
                    }
                    $('#job-summ-panel').empty().append(appendString);
                }
            });
        });

        $('#checkall').click(function(e) {
            if (this.checked) {
                $('input[type="checkbox"]').prop('checked', true);
            } else {
                $('input[type="checkbox"]').prop('checked', false);
            }
        });

        $('#checkalledit').click(function(e) {
            if (this.checked) {
                $('input[type="checkbox"]').prop('checked', true);
            } else {
                $('input[type="checkbox"]').prop('checked', false);
            }
        });

        $('body').on('click', '#btnedit', function() {
            let id = $(this).attr('data-id');
            $.ajax({
                type: "post",
                url: "{!! route('editrole') !!}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    $('#id').val(response.role.id);
                    $('#nameedit').val(response.role.name);
                    var appendString = "";
                    var lewat = 0;
                    for (var i = 0; i < response.permission.length; i++) {
                        for (var a = 0; a < response.rp.length; a++) {
                            if (response.permission[i].id == response.rp[a].permission_id) {
                                appendString += "<input type='checkbox' name='perm' id='perm' value='" + response.permission[i].id + "' checked>&emsp;" + response.permission[i].name + "<br>";
                                lewat = 1;
                            }
                        }
                        if (lewat == 0) {
                            appendString += "<input type='checkbox' name='perm' id='perm' value='" + response.permission[i].id + "' >&emsp;" + response.permission[i].name + "<br>";
                        }
                        lewat = 0;
                    }
                    $('#job-summ-panel2').empty().append(appendString);
                    $('#editrole').modal({
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
            let name = $('#name').val();

            var permission = [];
            $("input:checkbox[name=mycheckbok]:checked").each(function() {
                permission.push($(this).val());
            });
            permissions = JSON.stringify(permission);
            let aktif = 1
            if (name == '' || name == null) {
                $('#btnsimpan').html('Simpan')
                $('#btnsimpan').prop('disabled', false);
                notifalert('Nama');
            } else {
                $.ajax({
                    type: "post",
                    url: (id == null || id == '') ? "{!! route('storerole') !!}" : "{!! route('updaterole') !!}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'id': id,
                        'name': name,
                        'aktif': aktif,
                        'permissions': permissions,
                        'created_by': username,
                        'updated_by': username
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
                                $('#roleform').trigger("reset");
                                $('#addrole').modal('hide');
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

        $('#btnupdate').click(function(e) {
            $('#btnupdate').html('<i class="fas fa-hourglass"></i> Please Wait')
            $('#btnupdate').prop('disabled', true);
            let id = $('#id').val();
            let name = $('#nameedit').val();
            var permission = [];
            $("input:checkbox[name=perm]:checked").each(function() {
                permission.push($(this).val());
            });
            permissions = JSON.stringify(permission);
            let aktif = 1
            if (name == '' || name == null) {
                $('#btnupdate').html('Simpan')
                $('#btnupdate').prop('disabled', false);
                notifalert('Nama');
            } else {
                $.ajax({
                    type: "post",
                    url: "{!! route('updaterole') !!}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'id': id,
                        'name': name,
                        'aktif': aktif,
                        'permissions': permissions,
                        'created_by': username,
                        'updated_by': username
                    },
                    dataType: "json",
                    success: function(response) {
                        $('#btnupdate').html('Simpan');
                        $('#btnupdate').prop('disabled', false);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: (response.status == 'error') ? 'error' : 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then((result) => {
                            if (response.status == 'success') {
                                $('#btnupdate').html('Simpan');
                                $('#btnupdate').prop('disabled', false);
                                $('#editroleform').trigger("reset");
                                $('#editrole').modal('hide');
                                $("#datatables").DataTable().ajax.reload(null, false);
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        $('#btnupdate').html('Simpan');
                        $('#btnupdate').prop('disabled', false);
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
                        url: "{!! url('hapusrole') !!}",
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

        function notifalert2(params) {
            Swal.fire({
                title: 'Informasi',
                text: params + ' Tidak Sama',
                icon: 'warning'
            });
            return;
        }
    });
</script>
@endsection