@extends('admin.admin')

@section('title')
Master Data Produk
@endsection

@section('content_header')
Data Produk
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @can('produk-create')
                <button type="button" class="btn btn-primary" id="btntambah">
                    Tambah
                </button><br><br>
                @endcan
                <table class="table table-hover table-bordered table-stripped" id="datatables">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kode</th>
                            <th>Nama Produk</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Satuan</th>
                            <th>Qty Pcs</th>
                            <th>Harga Beli/Pcs</th>
                            <th>Harga Jual/Pcs</th>
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
<div class="modal fade" id="addproduk" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="produkform" name="produkform" class="form-horizontal" method="post">
                    @csrf
                    <input type="hidden" id="id">
                    <table width="700" align="center">
                        <tr>
                            <td style="padding-left:40px">
                                <div class="form-group kode_edit">
                                    <label>Kode Produk</label>
                                    <input type="text" class="form-control" id="kode" placeholder="Kode Produk" name="kode" style="width:150px;" readonly>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:40px">
                                <div class="form-group">
                                    <label>Nama Produk</label>
                                    <input type="text" class="form-control" id="nama" placeholder="Nama Produk" name="nama" style="width:250px;">
                                </div>
                                <div class="form-group">
                                    <label>Satuan</label>
                                    {!! Form::select('sat', $sat,$uom, array('class' => 'form-control', 'id'=>'uom', 'style'=>'width:250px;')) !!}
                                </div>
                                <div class="form-group">
                                    <label>Harga Beli</label>
                                    <input type="number" class="form-control" id="hargabeli" placeholder="Harga Beli" min=0 name="hargabeli" style="width:250px;">
                                </div>
                                <div class="form-group">
                                    <label>Harga Jual</label>
                                    <input type="number" class="form-control" id="hargajual" placeholder="Harga Jual" min=0 name="hargajual" style="width:250px;">
                                </div>
                            </td>
                            <td style="padding-left:60px">
                                <br><br><br>
                                <div class="form-group">
                                    <label id="qtypcslbl">Qty Per Pcs</label>
                                    <input type="number" class="form-control" id="qtypcs" placeholder="Qty Per Pcs" name="qtypcs" value=0 min=0 style="width:150px;">
                                </div>
                                <div class="form-group">
                                    <label id="hargabelipcslbl">Harga Beli/Pcs</label>
                                    <input type="number" class="form-control" id="hargabelipcs" min=0 placeholder="Harga Beli Per Pcs" name="hargabelipcs" readonly style="width:250px;">
                                </div>
                                <div class="form-group">
                                    <label id="hargajualpcslbl">Harga Jual/Pcs</label>
                                    <input type="number" class="form-control" id="hargajualpcs" min=0 placeholder="Harga Jual Per Pcs" name="hargajualpcs" style="width:250px;">
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

        $('#uom').change(function() {
            let satuan = $('#uom').val()
            $.ajax({
                url: "{!! route('getqtyunit') !!}",
                type: 'get',
                data: {
                    "_token": "{{ csrf_token() }}",
                    satuan: satuan
                },
                dataType: 'json',
                success: function(response) {
                    if ($('#uom').val() == "Pcs") {
                        $('#qtypcs').hide()
                        $('#hargabelipcs').hide()
                        $('#hargajualpcs').hide()
                        $('#qtypcslbl').hide()
                        $('#hargabelipcslbl').hide()
                        $('#hargajualpcslbl').hide()
                    } else if ($('#uom').val() != "Pack" && $('#uom').val() != "Pcs") {
                        $('#qtypcs').val(response.data.qty)
                        $('#qtypcs').get(0).disabled = true
                        $('#qtypcs').show()
                        $('#hargabelipcs').show()
                        $('#hargajualpcs').show()
                        $('#qtypcslbl').show()
                        $('#hargabelipcslbl').show()
                        $('#hargajualpcslbl').show()
                    } else {
                        $('#qtypcs').show()
                        $('#qtypcs').val('')
                        $('#qtypcs').get(0).disabled = false
                        $('#hargabelipcs').show()
                        $('#hargajualpcs').show()
                        $('#qtypcslbl').show()
                        $('#hargabelipcslbl').show()
                        $('#hargajualpcslbl').show()
                    }
                }
            });
        });

        $("#hargabeli, #qtypcs").keyup(function() {
            var hargabeli = $("#hargabeli").val();
            var qtypcs = $("#qtypcs").val();
            var totalamt = parseInt(hargabeli) / parseInt(qtypcs);
            $("#hargabelipcs").val(totalamt);
        });

        $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('dataproduk') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'kode',
                    name: 'kode'
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'hargabeli',
                    render: $.fn.dataTable.render.number(',', '.', 0, ''),
                    name: 'hargabeli'
                },
                {
                    data: 'hargajual',
                    render: $.fn.dataTable.render.number(',', '.', 0, ''),
                    name: 'hargajual'
                },
                {
                    data: 'satuan',
                    name: 'satuan'
                },
                {
                    data: 'qtypcs',
                    render: $.fn.dataTable.render.number(',', '.', 0, ''),
                    name: 'qtypcs'
                },
                {
                    data: 'hargabelipcs',
                    render: $.fn.dataTable.render.number(',', '.', 0, ''),
                    name: 'hargabelipcs'
                },
                {
                    data: 'hargajualpcs',
                    render: $.fn.dataTable.render.number(',', '.', 0, ''),
                    name: 'hargajualpcs'
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
            $('#kode').val('');
            $('#nama').val('');
            $('#qtypcs').val('');
            $('#hargabeli').val('');
            $('#hargabelipcs').val('');
            $('#hargajual').val('');
            $('#hargajualpcs').val('');
            $('#uom').val('');
            $('#qtypcs').show()
            $('#qtypcs').get(0).disabled = false
            $('#hargabelipcs').show()
            $('#hargajualpcs').show()
            $('#qtypcslbl').show()
            $('#hargabelipcslbl').show()
            $('#hargajualpcslbl').show()
            $('#exampleModalLabel').text('Tambah Produk');
            $('#addproduk').modal({
                show: true,
                backdrop: 'static'
            });
        });

        $('#btnsimpan').click(function(e) {
            $('#btnsimpan').html('<i class="fas fa-hourglass"></i> Please Wait')
            $('#btnsimpan').prop('disabled', true);
            let id = $('#id').val();
            let kode = $('#kode').val();
            let nama = $('#nama').val();
            let beli = $('#hargabeli').val();
            let jual = $('#hargajual').val();
            let satuan = $('#uom').val();
            let belipcs = 0;
            let jualpcs = 0;
            let qtypcs = 0;
            if ($('#uom').val() == "Pcs") {
                belipcs = $('#hargabeli').val();
                jualpcs = $('#hargajual').val();
                qtypcs = 1;
            } else {
                belipcs = $('#hargabelipcs').val();
                jualpcs = $('#hargajualpcs').val();
                qtypcs = $('#qtypcs').val();
            }
            if (nama == '' || nama == null) {
                $('#btnsimpan').html('Simpan')
                $('#btnsimpan').prop('disabled', false);
                notifalert('Nama');
            } else if (beli == '' || beli == null) {
                $('#btnsimpan').html('Simpan')
                $('#btnsimpan').prop('disabled', false);
                notifalert('Harga Beli');
            } else if (belipcs == '' || belipcs == null && $('#uom').val() != "Pcs") {
                $('#btnsimpan').html('Simpan')
                $('#btnsimpan').prop('disabled', false);
                notifalert('Harga Beli Pcs');
            } else if (jual == '' || jual == null) {
                $('#btnsimpan').html('Simpan')
                $('#btnsimpan').prop('disabled', false);
                notifalert('Harga Jual');
            } else if (satuan == '' || satuan == null) {
                $('#btnsimpan').html('Simpan')
                $('#btnsimpan').prop('disabled', false);
                notifalert('Satuan');
            } else if (jualpcs == '' || jualpcs == null && $('#uom').val() != "Pcs") {
                $('#btnsimpan').html('Simpan')
                $('#btnsimpan').prop('disabled', false);
                notifalert('Harga Jual Pcs');
            } else if (qtypcs == '' || qtypcs == null && $('#uom').val() != "Pcs") {
                $('#btnsimpan').html('Simpan')
                $('#btnsimpan').prop('disabled', false);
                notifalert('Quantity Pcs');
            } else {
                $.ajax({
                    type: "post",
                    url: (id == null || id == '') ? "{!! route('simpanproduk') !!}" : "{!! route('updateproduk') !!}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'id': id,
                        'kode': kode,
                        'nama': nama,
                        'hargabeli': beli,
                        'hargabelipcs': belipcs,
                        'hargajual': jual,
                        'hargajualpcs': jualpcs,
                        'qtypcs': qtypcs,
                        'satuan': satuan
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
                                $('#produkform').trigger("reset");
                                $('#addproduk').modal('hide');
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
            $('#exampleModalLabel').text('Edit Produk');
            let id = $(this).attr('data-id');
            console.log('cek', $(this).attr('data-id'))
            $.ajax({
                type: "post",
                url: "{!! route('editproduk') !!}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    let dataprod = response.data;
                    $('#id').val(dataprod.id);
                    $('#kode').val(dataprod.kode);
                    $('#nama').val(dataprod.nama);
                    $('#qtypcs').val(dataprod.qtypcs);
                    $('#uom').val(dataprod.satuan);
                    $('#hargabeli').val(dataprod.hargabeli);
                    $('#hargabelipcs').val(dataprod.hargabelipcs);
                    $('#hargajual').val(dataprod.hargajual);
                    $('#hargajualpcs').val(dataprod.hargajualpcs);
                    $('#addproduk').modal({
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
                        url: "{!! url('hapusproduk') !!}",
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