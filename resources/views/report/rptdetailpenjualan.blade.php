@extends('admin.admin')

@section('title', 'Report Detail Penjualan')

@section('content_header','Detail Data Penjualan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                Tanggal Transaksi &nbsp;&nbsp;&nbsp;
                <input type="date" value="<?php echo date('Y-m-d'); ?>" id="date1" name="date1">
                &nbsp;&nbsp;&nbsp; - &nbsp;&nbsp;&nbsp;
                <input type="date" value="<?php echo date('Y-m-d'); ?>" id="date2" name="date2">
                &nbsp;&nbsp;&nbsp;
                <button type="button" class="btn btn-primary" id="btnsearch">
                    Cari
                </button>
                <br><br>
                <table class="table table-hover table-bordered table-stripped" id="datatables">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Invoice</th>
                            <th>Kepada</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Keterangan</th>
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
<div class="modal fade" id="detailproduk" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="produkform" name="produkform" class="form-horizontal" method="post">
                    @csrf
                    <input type="hidden" id="id">
                    <table>
                        <tr>
                            <td style="padding-left:20px;padding-right:10px">
                                <label>Tanggal Transaksi</label>
                            </td>
                            <td> <label> : </label> </td>
                            <td><label id="tgl" name="tgl"></td>
                        </tr>
                        <tr>
                            <td style="padding-left:20px">
                                <label>No Invoice</label>
                            </td>
                            <td> <label> : </label> </td>
                            <td><label id="noinv" name="noinv"></td>
                        </tr>
                        <tr>
                            <td style="padding-left:20px">
                                <label>Kepada</label>
                            </td>
                            <td> <label> : </label> </td>
                            <td><label id="kepada" name="kepada"></td>
                        </tr>
                        <tr>
                            <td style="padding-left:20px">
                                <label>Status</label>
                            </td>
                            <td> <label> : </label> </td>
                            <td><label id="dp" name="dp"></td>
                        </tr>
                        <tr>
                            <td style="padding-left:20px">
                                <label>Keterangan</label>
                            </td>
                            <td> <label> : </label> </td>
                            <td><label id="lunas" name="lunas"></td>
                        </tr>
                    </table>
                    <br>
                    <table class="table table-hover table-bordered table-stripped" id="datatables2" width="100%">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Produk</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <br>
                    <table align="right">
                        <tr>
                            <td><label>Sub Total</label></td>
                            <td> <label> : </label> </td>
                            <td align="right"><label id="total" name="total"></td>
                        </tr>
                        <tr>
                            <td><label>Diskon</label></td>
                            <td> <label> : </label> </td>
                            <td align="right"><label id="diskon" name="diskon"></td>
                        </tr>
                        <tr>
                            <td><label>Total</label></td>
                            <td> <label> : </label> </td>
                            <td align="right"><label id="ttlamt" name="ttlamt"></td>
                        </tr>
                        <tr>
                            <td><br><label id="lbldp" name="lbldp">DP</label></td>
                            <td><br> <label> : </label> </td>
                            <td align="right"><br><label id="dpbyr" name="dpbyr"></td>
                        </tr>
                        <tr>
                            <td><label id="lblhis" name="lblhis">History Bayar</label></td>
                            <td> <label id="titik" name="titik"> : </label> </td>
                            <td align="right"><label id="hisbayar" name="hisbayar"></td>
                        </tr>
                        <tr>
                            <td><label id="lblkurang" name="lblkurang">Kurang</label></td>
                            <td> <label> : </label> </td>
                            <td align="right"><label id="kurang" name="kurang"></td>
                        <tr>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btncetak">Print</button>
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
            ajax: {
                url: "{{ route('detailpenjualan') }}",
                data: function(d) {
                    d.date1 = $('#date1').val()
                    d.date2 = $('#date2').val()
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'at',
                    name: 'created_at'
                },
                {
                    data: 'noinv',
                    name: 'noinv'
                },
                {
                    data: 'kepada',
                    name: 'kepada'
                },
                {
                    data: 'totalamountdiskon',
                    render: $.fn.dataTable.render.number(',', '.', 0, ''),
                    name: 'totalamountdiskon'
                },
                {
                    data: 'dp',
                    render: function(data) {
                        if (data == 0) {
                            return 'Tunai'
                        } else {
                            return 'DP'
                        }

                    },
                    name: 'dp'
                },
                {
                    data: 'lunas',
                    render: function(data) {
                        if (data == 1) {
                            return 'Lunas'
                        } else {
                            return 'Belum Lunas'
                        }

                    },
                    name: 'lunas'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('#btnsearch').click(function(e) {
            $("#datatables").DataTable().ajax.reload(null, false);
        });

        $('body').on('click', '#btndetail', function() {
            $('#datatables2').dataTable().fnClearTable();
            $("#datatables2").dataTable().fnDestroy();
            $('#tgl').text('');
            $('#noinv').text('');
            $('#kepada').text('');
            $('#dp').text('');
            $('#lbldp').text('Bayar');
            $('#lblkurang').text('');
            $('#kurang').text('');
            $('#lunas').text('');
            $('#total').text('');
            $('#diskon').text('');
            $('#ttlamt').text('');
            $('#dpbyr').text('');
            $('#hisbayar').text('');
            let noinv = $(this).attr('data-id');
            $('#datatables2').DataTable({
                order: [],
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('detailjual') }}",
                    data: function(d) {
                        d.inv = noinv
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'qty',
                        name: 'qty'
                    },
                    {
                        data: 'satuan',
                        name: 'satuan'
                    },
                    {
                        data: 'harga',
                        name: 'harga'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                ],
                "drawCallback": function(settings) {
                    let dataku = settings.json.data
                    if (dataku == [] || dataku == '') {
                        return;
                    } else {
                        const formatter = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                        });
                        $('#tgl').text(dataku[0].at);
                        $('#noinv').text(dataku[0].noinv);
                        $('#kepada').text(dataku[0].kepada);
                        if (dataku[0].dp == 0) {
                            $('#dp').text('Tunai');
                            $('#lbldp').text('Bayar');
                            $('#hisbayar').text('');
                            $('#titik').text('');
                            $('#lblhis').text('');
                        } else {
                            $('#dp').text('DP');
                            $('#lbldp').text('DP');
                            $('#hisbayar').text(formatter.format(dataku[0].bayarlunas));
                            $('#titik').text(':');
                            $('#lblhis').text('History Bayar');
                        }

                        blunas = dataku[0].bayarlunas
                        if (blunas == null) {
                            blunas = 0
                        }

                        if (dataku[0].totalamountdiskon - dataku[0].bayar >= blunas && dataku[0].dp == 1) {
                            $('#lblkurang').text('Kurang');
                            $('#kurang').text(formatter.format(dataku[0].totalamountdiskon - dataku[0].bayar - blunas));
                        } else {
                            $('#lblkurang').text('Kembali');
                            $('#kurang').text(formatter.format((blunas + dataku[0].bayar) - dataku[0].totalamountdiskon));
                        }

                        if (dataku[0].lunas == 0) {
                            $('#lunas').text('Belum Lunas');
                        } else {
                            $('#lunas').text('Lunas');
                        }
                        $('#total').text(formatter.format(dataku[0].totalamount));
                        $('#diskon').text(formatter.format(dataku[0].diskon));
                        $('#ttlamt').text(formatter.format(dataku[0].totalamountdiskon));
                        $('#dpbyr').text(formatter.format(dataku[0].bayar));
                        $('#detailproduk').modal({
                            show: true,
                            backdrop: 'static'
                        });
                    }
                },
            });
        });

        $('body').on('click', '#btnprint', function() {
            let noinv = $(this).attr('data-id');
            let encryptinv = window.btoa(noinv);
            window.open("{!! url('cetakrpt') !!}" + "/" + encryptinv, "_blank");
        });

        $('#btncetak').click(function(e) {
            let noinv = $('#noinv').text();
            let encryptinv = window.btoa(noinv);
            window.open("{!! url('cetakrpt') !!}" + "/" + encryptinv, "_blank");
        });

    })
</script>
@endsection