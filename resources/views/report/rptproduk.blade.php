@extends('admin.admin')

@section('title', 'Report Produk')

@section('content_header','List Data Produk')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-hover table-bordered table-stripped" id="datatables">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Produk</th>
                            <th>Harga Jual Per Pcs</th>
                            <th>Harga Jual Per Pack</th>
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
                url: "{{ route('daftarharga') }}"
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
                    data: 'hargajualpcs',
                    render: $.fn.dataTable.render.number(',', '.', 0, ''),
                    name: 'hargajualpcs'
                },
                {
                    data: 'hargajual',
                    render: $.fn.dataTable.render.number(',', '.', 0, ''),
                    name: 'hargajual'
                },
            ]
        });

    })
</script>
@endsection