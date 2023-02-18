@extends('admin.admin2')

@section('title')
Struck
@endsection

@section('content')
<div class="col-md-12">
    <div class="card text-justify" style="width: 11cm; position: center;">
        <div class="card-header text-center" style="font-size: 11pt">
            <img src="{{ asset('images/logo.png') }}" width="25%" class="rounded" alt="..."><br>
            Karangduren, RT 005 RW 001, Karangduren, Kec. Tengaran <br>
            (Belakang Pasar Kembangsari, Samping SMKN 1 Tengaran) <br>
            No. HP / WA : (+62) 858-7636-2331
        </div>
        <div class="card-body" style="overflow-y: auto;">
            <table style=" font-size: 11pt">
                <tr>
                    <td>Kepada :</td>
                    <td>{{ $data[0]->kepada }}</td>
                </tr>
                <tr>
                    <td>Tanggal :</td>
                    <td>{{ date('d F Y', strtotime($data[0]->created_at)) }}</td>
                </tr>
                <tr>
                    <td>Invoice :</td>
                    <td>{{ $data[0]->noinv }}</td>
                </tr>
            </table>
            <table style="font-size: 11pt;width: 9.5cm" border="1">
                <tr>
                    <td><b>Nama Barang</b></td>
                    <td><b>Qty</b></td>
                    <td><b>Unit</b></td>
                    <td><b>Harga</b></td>
                    <td><b>Amount</b></td>
                </tr>
                @foreach ($data as $item)
                <tr>
                    <td align="left">{{ $item->nama }} {{ $item->ket }}</td>
                    <td align="right">{{ number_format($item->qty, 0, '.', ',') }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td align="right">{{ number_format($item->harga, 0, '.', ',') }}</td>
                    <td align="right">{{ number_format($item->amount, 0, '.', ',') }}</td>
                </tr>
                @endforeach
            </table>
            @if($data[0]->lunas == 1)
            <img src="{{ asset('images/lunas.jpg') }}" width="40%" alt="">
            @endif
            <div class="float-right">
                <table style="font-size: 11pt">
                    <tr>
                        <td width="60">Sub Total</td>
                        <td width="5">:</td>
                        <td width="10">Rp</td>
                        <td align="right">{{ number_format($data[0]->totalamount, 0, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td>Diskon</td>
                        <td>:</td>
                        <td>Rp</td>
                        <td align="right">{{ number_format($data[0]->diskon, 0, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td><b>Total</b></td>
                        <td>:</td>
                        <td>Rp</td>
                        <td align="right">{{ number_format($data[0]->totalamount-$data[0]->diskon, 0, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td><b>DP</b></td>
                        <td>:</td>
                        <td>Rp</td>
                        <td align="right">
                            @if($data[0]->dp==1)
                            {{ number_format($data[0]->bayar, 0, '.', ',') }}
                            @else
                            0
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><b>Bayar</b></td>
                        <td>:</td>
                        <td>Rp</td>
                        <td align="right">
                            @if($data[0]->dp==1)
                            {{ number_format($data[0]->bayardp, 0, '.', ',') }}
                            @else
                            {{ number_format($data[0]->bayar, 0, '.', ',') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @if(($data[0]->totalamountdiskon-$data[0]->bayar) >= $data[0]->bayardp && $data[0]->dp==1)
                            Kurang
                            @else
                            Kembali
                            @endif
                        </td>
                        <td>:</td>
                        <td>Rp</td>
                        <td align="right">
                            @if(($data[0]->totalamountdiskon-$data[0]->bayar) >= $data[0]->bayardp && $data[0]->dp==1)
                            {{ number_format($data[0]->totalamountdiskon-$data[0]->bayar-$data[0]->bayardp, 0, '.', ',') }}
                            @else
                            {{ number_format(($data[0]->bayardp + $data[0]->bayar)-$data[0]->totalamountdiskon, 0, '.', ',') }}
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <small>
            <center>
                Terima Kasih Atas Kunjungan Anda<br>
                Barang Yang sudah dibeli tidak dapat dikembalikan lagi<br>
                {{ date('Y-m-d H:i:s') }}
            </center>
        </small>
    </div>
</div>
<script>
    window.print()
</script>
@endsection