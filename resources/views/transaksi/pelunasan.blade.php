@extends('admin.admin')

@section('title')
Pelunasan
@endsection

@section('content_header')
Proses Transaksi Pelunasan
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <table>
                        <tr>
                            <td>
                                <label>No Invoice</label>
                            </td>
                            <td width="200">
                                <select class="form-control select2" id="noinv" name="noinv">

                                </select>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary" id="btnsearch" name="btnsearch">Search</button>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <table class="table table-hover table-bordered table-stripped" id="datatables">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Produk</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Amount(Rp.)</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <br>
                    <div class="row">
                        <label>Kepada</label>&nbsp;&nbsp;
                        <input type="text" class="form-control" id="kepada" placeholder="Kepada" name="kepada" style="width: 200px;" readonly>
                    </div>
                    <table align="right">
                        <tr>
                            <td>
                                <label>Sub Total</label>
                            </td>
                            <td>
                                <input type="number" class="form-control" id="subtotal" placeholder="Sub Total" name="subtotal" style="text-align: right;" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>Diskon</label>
                            </td>
                            <td>
                                <input type="number" class="form-control" id="diskon" placeholder="Diskon" name="diskon" min=0 style="text-align: right;" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>Total Amount</label>
                            </td>
                            <td>
                                <input type="number" class="form-control" id="ttlamt" placeholder="Total Amount" name="ttlamt" min=0 style="text-align: right;" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label id="dplbl">DP</label>
                            </td>
                            <td>
                                <input type="number" class="form-control" id="dp" placeholder="DP" name="dp" style="text-align: right;" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label id="hisbayarlbl">History Bayar</label>
                            </td>
                            <td>
                                <input type="number" class="form-control" id="hisbayar" placeholder="History Bayar" name="hisbayar" style="text-align: right;" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label id="kuranglbl">Kurang</label>
                            </td>
                            <td>
                                <input type="number" class="form-control" id="kurang" placeholder="Kurang" name="kurang" style="text-align: right;" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label id="bayarlbl">Bayar</label>
                            </td>
                            <td>
                                <input type="number" class="form-control" id="bayar" placeholder="Bayar" name="bayar" style="text-align: right;">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label id="kembalilbl">Kembali</label>
                            </td>
                            <td>
                                <input type="number" class="form-control" id="kembali" placeholder="Kembali" name="kembali" style="text-align: right;" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" align="right">
                                <br>
                                @can('lunas-create')
                                <button type="button" class="btn btn-primary" id="btnsimpan">Simpan</button>
                                @endcan
                                @can('lunas-print')
                                <a href="{{ route('cetakdatalunas') }}" target="_blank" class="btn btn-danger">
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

        $("#noinv").select2({
            placeholder: '-- Pilih Invoice --',
            ajax: {
                url: "{{route('getinv')}}",
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
        var noinv = $('#noinv').val();
        $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                type: "post",
                url: "{!! route('listprodukdp') !!}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    noinv: noinv
                },
                dataType: "json",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'nama',
                    name: 'nama'
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
            ]
        });

        $('#btnsearch').click(function(e) {
            let noinv = $('#noinv').val();
            if (noinv == '' || noinv == null) {
                $('#btnsearch').html('Search')
                $('#btnsearch').prop('disabled', false);
                notifalert('No Invoice');
            } else {
                $.ajax({
                    type: "post",
                    url: "{!! route('listprodukdp') !!}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        noinv: noinv
                    },
                    dataType: "json",
                    success: function(response) {
                        let dataprod = response.data;
                        $('#bayar').val('')
                        $('#kepada').val(dataprod[0].kepada)
                        $('#subtotal').val(dataprod[0].totalamount)
                        if (dataprod[0].diskon == null) {
                            dataprod[0].diskon = 0
                        }
                        $('#diskon').val(dataprod[0].diskon)
                        $('#ttlamt').val(dataprod[0].totalamount - dataprod[0].diskon)
                        $('#dp').val(dataprod[0].bayar)
                        if (dataprod[0].aktiflunas == 0) {
                            dataprod[0].bayardp = 0
                        } else {
                            if (dataprod[0].bayardp == null) {
                                dataprod[0].bayardp = 0
                            }
                        }
                        $('#hisbayar').val(dataprod[0].bayardp)
                        $('#kurang').val(dataprod[0].kurangkembali - dataprod[0].bayardp)
                        $("#datatables").dataTable().fnDestroy();
                        $('#datatables').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: {
                                type: "post",
                                url: "{!! route('listprodukdp') !!}",
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                    noinv: noinv
                                },
                                dataType: "json",
                            },
                            columns: [{
                                    data: 'DT_RowIndex',
                                    name: 'DT_RowIndex'
                                },
                                {
                                    data: 'nama',
                                    name: 'nama'
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
                            ]
                        });
                        $('#btnsearch').html('Search')
                        $('#btnsearch').prop('disabled', false);
                    }
                });
            }
        });

        $("#bayar").keyup(function() {
            var bayar = $('#bayar').val();
            var kurang = $("#kurang").val();
            if (bayar == [] || bayar == '') {
                bayar = 0;
            }
            if (parseInt(bayar) > parseInt(kurang)) {
                var total = parseInt(bayar) - parseInt(kurang);

                $("#kembali").val(total);
                $('#kembalilbl').text('Kembali')
            } else {
                var total = parseInt(kurang) - parseInt(bayar);
                $("#kembali").val(total);
                $('#kembalilbl').text('Kekurangan')
            }
        });

        $('#btnsimpan').click(function(e) {
            $('#btnsimpan').html('<i class="fas fa-hourglass"></i> Please Wait')
            $('#btnsimpan').prop('disabled', true);
            let noinv = $('#noinv').val();
            let hisbayar = $('#hisbayar').val();
            let kurang = $('#kurang').val();
            let bayar = $('#bayar').val();

            if (bayar == '' || bayar == [] || bayar == 0) {
                $('#btnsimpan').html('Simpan')
                $('#btnsimpan').prop('disabled', false);
                notifalert('Pembayaran')
            } else {
                $.ajax({
                    type: "post",
                    url: "{{ route('addlunas') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        noinv: noinv,
                        hisbayar: hisbayar,
                        kurang: kurang,
                        bayar: bayar
                    },
                    dataType: "json",
                    success: function(response) {
                        Swal.fire({
                            icon: (response.status == 'error') ? 'error' : 'success',
                            title: response.title,
                            text: response.message
                        }).then((result) => {
                            (response.status == 'success') ? window.location.replace("{{ route('translunas') }}"): ''
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