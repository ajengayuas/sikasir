@extends('admin.admin')

@section('title', 'Report Keuangan')

@section('content_header','Data Keuangan')

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
                            <th>Total Amount Jual</th>
                            <th>Total Amount Beli</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <br>
                <table>
                    <tr>
                        <td><label>Grand Total Amount Jual</label></td>
                        <td> <label> : </label> </td>
                        <td align="right"><label id="amtjual" name="amtjual"></td>
                    </tr>
                    <tr>
                        <td><label>Grand Total Amount Beli</label></td>
                        <td> <label> : </label> </td>
                        <td align="right"><label id="amtbeli" name="amtbeli"></td>
                    </tr>
                    <tr>
                        <th colspan="3">
                            <hr class="border border-primary border-3 opacity-75">
                        </th>
                    </tr>
                    <tr>
                        <td><label id="lbllaba" name="lbllaba">Laba</label></td>
                        <td> <label> : </label> </td>
                        <td align="right"><label id="laba" name="laba"></td>
                    </tr>
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

        amount();

        var tabel = $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('listdatakeu') }}",
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
                    data: 'totalamountdiskon',
                    render: $.fn.dataTable.render.number(',', '.', 0, ''),
                    name: 'totalamountdiskon'
                },
                {
                    data: 'totalamountbeli',
                    render: $.fn.dataTable.render.number(',', '.', 0, ''),
                    name: 'totalamountbeli'
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
            ],
        });

        $('#btnsearch').click(function(e) {
            tabel.draw();
            amount();
        });

        function amount() {
            $.ajax({
                type: "post",
                url: "{{ route('getlaba') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    date1: $('#date1').val(),
                    date2: $('#date2').val()
                },
                dataType: "json",
                success: function(response) {
                    const formatter = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                    });

                    $('#amtbeli').text(formatter.format(response.beli))
                    $('#amtjual').text(formatter.format(response.jual))
                    $('#lbllaba').text(response.ket);
                    $('#laba').text(formatter.format(response.hasil));

                }
            });
        }
    })
</script>
@endsection