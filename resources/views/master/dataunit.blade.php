@extends('admin.admin')

@section('title')
Master Data Satuan
@endsection

@section('content_header')
Data Satuan
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @can('unit-create')
                <button type="button" class="btn btn-primary" id="btntambah">
                    Tambah
                </button><br><br>
                @endcan
                <table class="table table-hover table-bordered table-stripped" id="datatables">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Satuan</th>
                            <th>Qty Pcs</th>
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
<div class="modal fade" id="adduom" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Satuan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="uomform" name="uomform" class="form-horizontal" method="post">
                    @csrf
                    <input type="hidden" id="id">
                    <table width="700" align="center">
                        <tr>
                            <td style="padding-left:40px">
                                <div class="form-group">
                                    <label>Satuan</label>
                                    <input type="text" class="form-control" id="satuan" placeholder="Satuan" name="satuan" style="width:250px;">
                                </div>
                                <div class="form-group">
                                    <label>Qty Pcs</label>
                                    <input type="number" class="form-control" id="qty" placeholder="Qty Pcs" min=0 name="qty" style="width:250px;">
                                </div>
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
            ajax: "{{ route('datauom') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'satuan',
                    name: 'satuan'
                },
                {
                    data: 'qty',
                    render: $.fn.dataTable.render.number(',', '.', 0, ''),
                    name: 'qty'
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
            $('.kode_edit').hide();
            $('#id').val('');
            $('#satuan').val('');
            $('#qty').val('');
            $('#exampleModalLabel').text('Tambah Satuan');
            $('#adduom').modal({
                show: true,
                backdrop: 'static'
            });
        });

        $('#btnsimpan').click(function(e) {
            $('#btnsimpan').html('<i class="fas fa-hourglass"></i> Please Wait')
            $('#btnsimpan').prop('disabled', true);
            let id = $('#id').val();
            let satuan = $('#satuan').val();
            let qty = $('#qty').val();
            if (satuan == '' || satuan == null) {
                $('#btnsimpan').html('Simpan')
                $('#btnsimpan').prop('disabled', false);
                notifalert('Satuan');
            } else if (qty == '' || qty == null) {
                $('#btnsimpan').html('Simpan')
                $('#btnsimpan').prop('disabled', false);
                notifalert('Qty Pcs');
            } else {
                $.ajax({
                    type: "post",
                    url: (id == null || id == '') ? "{!! route('simpanuom') !!}" : "{!! route('updateuom') !!}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'id': id,
                        'satuan': satuan,
                        'qty': qty
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
                                $('#btnsimpan').html('Simpan')
                                $('#btnsimpan').prop('disabled', false);
                                $('#uomform').trigger("reset");
                                $('#adduom').modal('hide');
                                $("#datatables").DataTable().ajax.reload(null, false);
                            } else {
                                $('#btnsimpan').html('Simpan');
                                $('#btnsimpan').prop('disabled', false);
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Data Gagal Disimpan!',
                            text: 'Cek Data',
                            icon: 'error'
                        });
                        $('#btnsimpan').html('Simpan');
                        $('#btnsimpan').prop('disabled', false);
                        return;
                    }
                });
            }
        });

        $('body').on('click', '#btnedit', function() {
            $('#exampleModalLabel').text('Edit Satuan');
            let id = $(this).attr('data-id');
            $.ajax({
                type: "post",
                url: "{!! route('edituom') !!}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    let datauom = response.data;
                    $('#id').val(datauom.id);
                    $('#satuan').val(datauom.satuan);
                    $('#qty').val(datauom.qty);
                    $('#adduom').modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $('.kode_edit').show();
                }
            });
        });

        $('body').on('click', '#btndelete', function() {
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
                        url: "{!! url('hapusuom') !!}",
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