@extends('admin.admin')

@section('title')
Kasir
@endsection

@section('content_header')
Proses Transaksi
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="post" class="form-user" id="addform">
                    <div class="form-group">
                        <div align="right">
                            @can('kasir-delete')
                            <button type="button" class="btn btn-danger btn-xs" id="btnreset" name="btnreset">Hapus Semua</button>
                            @endcan
                        </div>
                        <table>
                            <tr>
                                <td>
                                    <label style="width: 100px;">Nama Produk</label>
                                </td>
                                <td colspan="2">
                                    <select class="form-control select2" style="width: 250px;" id="nama" name="nama">
                                        <option value=""></option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control select2" id="satuan" name="satuan">
                                        <option value="Pcs">Pcs</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" id="ket" placeholder="Keterangan" name="ket" style="width: 450px;">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Harga</label>
                                </td>
                                <td>
                                    <input type="number" class="form-control" id="harga" placeholder="Harga" name="harga" min=0 style="width: 150px;">
                                </td>
                                <td>
                                    <label style="width: 40px;" align="right">Qty</label>
                                </td>
                                <td>
                                    <input type="number" class="form-control" id="qty" placeholder="Qty Per Pcs" name="qty" value=1 min=0 style="width: 100px;">
                                </td>
                                <td>
                                    @can('kasir-create')
                                    &nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-primary" id="btntambah" name="btntambah">Tambah</button>
                                    @endcan
                                </td>
                            </tr>
                        </table>
                </form>
                <br>
                <table class="table table-hover table-bordered table-stripped" id="datatables">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Produk</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Amount(Rp.)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <br>
            <div class="row">
                <label>Kepada</label>&nbsp;&nbsp;
                <input type="text" class="form-control" id="kepada" placeholder="Kepada" name="kepada" style="width: 200px;">
            </div>
            <table align="right">
                <tr>
                    <td colspan="2">
                        <label>Sub Total</label>
                    </td>
                    <td>
                        <input type="number" class="form-control" id="subtotal" placeholder="Sub Total" name="subtotal" style="text-align: right;" readonly>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <label>Diskon</label>
                    </td>
                    <td>
                        <input type="number" class="form-control" id="diskon" placeholder="Diskon" name="diskon" min=0 style="text-align: right;">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <label>Total Amount</label>
                    </td>
                    <td>
                        <input type="number" class="form-control" id="ttlamt" placeholder="Total Amount" name="ttlamt" min=0 style="text-align: right;" readonly>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="icheck-primary d-inline">
                            <label>DP</label>
                            <input type="checkbox" id="cbdp">
                        </div>
                    </td>
                    <td>
                        <label id="bayarlbl">Bayar</label>
                    </td>
                    <td>
                        <input type="number" class="form-control" id="bayar" placeholder="Bayar" name="bayar" min=0 style="text-align: right;">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <label id="kembalilbl">Kembali</label>
                    </td>
                    <td>
                        <input type="number" class="form-control" id="kembali" placeholder="Kembali" name="kembali" style="text-align: right;" readonly>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" align="right">
                        <br>
                        @can('kasir-create')
                        <button type="button" class="btn btn-primary" id="btnsimpan">Simpan</button>
                        @endcan
                        @can('kasir-print')
                        <a href="{{ route('cetakdata') }}" target="_blank" class="btn btn-danger">
                            Print
                        </a>
                        @endcan
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
</div>
@endsection

@section('js')
<script type='text/javascript'>
    $(function() {
        $('.select2').select2()
    })

    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        amount();

        $("#nama").select2({
            placeholder: '-- Pilih Produk --',
            ajax: {
                url: "{{route('getproduk')}}",
                type: "post",
                dataType: 'json',
                data: function(params) {
                    return {
                        "_token": "{{ csrf_token() }}",
                        search: params.term
                    };
                },
                processResults: function(response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }
        });

        $('#satuan').change(function() {
            $('#btntambah').html('<i class="fas fa-hourglass"></i> Please Wait')
            $('#btntambah').prop('disabled', true);
            var sat = $(this).val();
            console.log('sat', sat);
            var id = $('#nama').val();
            console.log('id', id);
            $.ajax({
                url: "{!! route('dataharga') !!}",
                type: 'get',
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (sat == "Pcs") {
                        $('#harga').val(response['data'][0].hargajualpcs);
                    } else {
                        $('#harga').val(response['data'][0].hargajual);
                    }
                    $('#btntambah').html('Tambah');
                    $('#btntambah').prop('disabled', false);
                }
            });
        });

        $('#nama').change(function() {
            $('#btntambah').html('<i class="fas fa-hourglass"></i> Please Wait')
            $('#btntambah').prop('disabled', true);
            var id = $(this).val();
            $.ajax({
                url: "{!! route('getuom') !!}",
                type: 'get',
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response[0].text != "Pcs") {
                        $('#satuan').find('option').not(':first').remove();
                        var option = "<option value='" + response[0].id + "'>" + response[0].text + "</option>";
                        $("#satuan").append(option);
                    }
                    $.ajax({
                        url: "{!! route('dataharga') !!}",
                        type: 'get',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id: id
                        },
                        dataType: 'json',
                        success: function(response) {
                            var sat = $('#satuan').val();
                            console.log('cek satuan', sat);
                            if (sat == "Pcs") {
                                $('#harga').val(response['data'][0].hargajualpcs);
                            } else {
                                $('#harga').val(response['data'][0].hargajual);
                            }
                            $('#btntambah').html('Tambah');
                            $('#btntambah').prop('disabled', false);
                        }
                    });
                }
            });
        });

        $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('datatempkasir') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'satuan',
                    name: 'satuan'
                },
                {
                    data: 'harga',
                    render: $.fn.dataTable.render.number(',', '.', 0, ''),
                    name: 'harga'
                },
                {
                    data: 'qty',
                    render: $.fn.dataTable.render.number(',', '.', 0, ''),
                    name: 'qty'
                },
                {
                    data: 'amount',
                    render: $.fn.dataTable.render.number(',', '.', 0, ''),
                    name: 'amount'
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
            $('#btntambah').html('<i class="fas fa-hourglass"></i> Please Wait')
            $('#btntambah').prop('disabled', true);
            let nama = $('#nama').val();
            let satuan = $('#satuan').val();
            let harga = $('#harga').val();
            let qty = $('#qty').val();
            let ket = $('#ket').val();
            if (nama == '' || nama == null) {
                $('#btntambah').html('Tambah')
                $('#btntambah').prop('disabled', false);
                notifalert('Nama');
            } else if (satuan == '' || satuan == null) {
                $('#btntambah').html('Tambah')
                $('#btntambah').prop('disabled', false);
                notifalert('Satuan');
            } else if (harga == '' || harga == null) {
                $('#btntambah').html('Tambah')
                $('#btntambah').prop('disabled', false);
                notifalert('Harga');
            } else if (qty == '' || qty == null || qty == 0) {
                $('#btntambah').html('Tambah')
                $('#btntambah').prop('disabled', false);
                notifalert('Qty');
            } else {
                $.ajax({
                    type: "post",
                    url: "{!! route('tempkasir') !!}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'nama': nama,
                        'satuan': satuan,
                        'harga': harga,
                        'qty': qty,
                        'ket': ket
                    },
                    dataType: "json",
                    success: function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: (response.status == 'error') ? 'error' : 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1000
                        }).then((result) => {
                            if (response.status == 'success') {
                                $("#datatables").DataTable().ajax.reload(null, false);
                                amount();
                                $('#harga').val('');
                                $('#qty').val(1);
                                $('#nama').empty();
                                $('#ket').val('');
                                $('#btntambah').html('Tambah')
                                $('#btntambah').prop('disabled', false);
                            } else {
                                $('#btntambah').html('Tambah');
                                $('#btntambah').prop('disabled', false);
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
                });
            }
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
                console.log('result :>> ', result);
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "{!! url('hapustempkasir') !!}",
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
                                $("#datatables").DataTable().ajax.reload(null, false);
                                amount()
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

        function amount() {
            $.ajax({
                type: "get",
                url: "{{ route('getamount') }}",
                data: {},
                dataType: "json",
                success: function(response) {
                    $('#subtotal').val(response.data.totamount);
                    $("#ttlamt").val(response.data.totamount);
                }
            });
        }

        $("#diskon").keyup(function() {
            var subtot = $('#subtotal').val();
            var disk = $("#diskon").val();
            if (disk == [] || disk == '') {
                disk = 0;
            }
            var total = parseInt(subtot) - parseInt(disk);
            $("#ttlamt").val(total);
        });

        $('#cbdp').change(function(e) {
            if (this.checked) {
                $('#bayarlbl').html('DP');
                $('#kembalilbl').html('Kekurangan');
                var total = parseInt($('#ttlamt').val()) - parseInt($("#bayar").val());
                $("#kembali").val(total);
                $("#bayar").keyup(function() {
                    var totamount = $('#ttlamt').val();
                    var bayar = $("#bayar").val();
                    var total = parseInt(totamount) - parseInt(bayar);
                    $("#kembali").val(total);
                });
            } else {
                $('#bayarlbl').html('Bayar');
                $('#kembalilbl').html('Kembali');
                var total = parseInt($("#bayar").val()) - parseInt($('#ttlamt').val());
                $("#kembali").val(total);
                $("#bayar").keyup(function() {
                    var totamount = $('#ttlamt').val();
                    var bayar = $("#bayar").val();
                    var total = parseInt(bayar) - parseInt(totamount);
                    $("#kembali").val(total);
                });
            }
        });

        $("#bayar").keyup(function() {
            var totamount = $('#ttlamt').val();
            var bayar = $("#bayar").val();
            var total = parseInt(bayar) - parseInt(totamount);
            $("#kembali").val(total);
        });

        $('#btnsimpan').click(function(e) {
            $('#btnsimpan').html('<i class="fas fa-hourglass"></i> Please Wait')
            $('#btnsimpan').prop('disabled', true);
            let kpd = $('#kepada').val();
            let subtot = $('#subtotal').val();
            let disk = $('#diskon').val();
            let totamt = $('#ttlamt').val();
            let dbyr = $('#bayar').val();
            let kmbl = $('#kembali').val();

            if ($("#cbdp").is(":checked")) {
                cekbokdp = true;
            } else {
                cekbokdp = false;
            }
            console.log(cekbokdp);
            if (dbyr == '' || dbyr == []) {
                $('#btnsimpan').html('Simpan')
                $('#btnsimpan').prop('disabled', false);
                notifalert('Pembayaran')
            } else if (kmbl < 0) {
                $('#btnsimpan').html('Simpan')
                $('#btnsimpan').prop('disabled', false);
                notifalert2('Kembalian/Kekurangan')
            } else if (cekbokdp == false && dbyr == 0) {
                $('#btnsimpan').html('Simpan')
                $('#btnsimpan').prop('disabled', false);
                notifalert('Pembayaran')
            } else {
                $.ajax({
                    type: "post",
                    url: "{{ route('addtransaksi') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        kepada: kpd,
                        subtotal: subtot,
                        diskon: disk,
                        cekbokdp: cekbokdp,
                        pembayaran: dbyr,
                        kekurangan: kmbl
                    },
                    dataType: "json",
                    success: function(response) {
                        Swal.fire({
                            icon: (response.status == 'error') ? 'error' : 'success',
                            title: response.title,
                            text: response.message
                        }).then((result) => {
                            (response.status == 'success') ? window.location.replace("{{ route('datakasir') }}"): ''
                            $('#btnsimpan').html('Simpan')
                            $('#btnsimpan').prop('disabled', false);
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
                });
            }
        });

        $('#btnreset').click(function(e) {
            Swal.fire({
                title: 'Perhatian',
                text: "Apakah Anda Yakin Reset Data Transaksi ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Reset',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "{!! url('reset') !!}",
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        dataType: "json",
                        success: function(response) {
                            Swal.fire({
                                title: response.title,
                                icon: (response.status == 'error') ? 'error' : 'success',
                                text: response.message,
                            }).then((result) => {
                                $("#datatables").DataTable().ajax.reload(null, false);
                                amount()
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
                text: params + ' Tidak Boleh Minus',
                icon: 'warning'
            });
            return;
        }
    });
</script>
@endsection