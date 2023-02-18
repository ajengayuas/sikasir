@extends('admin.admin')

@section('title', 'Report Penjualan')

@section('content_header','List Data Penjualan')

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
                url: "{{ route('datapenjualan') }}",
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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'qty',
                    render: $.fn.dataTable.render.number(',', '.', 0, ''),
                    name: 'qty'
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
                    data: 'amount',
                    render: $.fn.dataTable.render.number(',', '.', 0, ''),
                    name: 'amount'
                },
            ]
        });

        $('#btnsearch').click(function(e) {
            $("#datatables").DataTable().ajax.reload(null, false);
        });

    })
</script>
@endsection