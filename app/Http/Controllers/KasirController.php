<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DataKasir;
use App\Models\DataProduk;
use App\Models\DataTempKasir;
use App\Models\DataUnit;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class KasirController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:kasir-list|kasir-create|kasir-edit|kasir-delete|kasir-print', ['only' => ['index', 'listtempkasir']]);
        $this->middleware('permission:kasir-print', ['only' => ['cetak']]);
        $this->middleware('permission:kasir-delete', ['only' => ['destroy', 'resetdata']]);
        $this->middleware('permission:kasir-create', ['only' => ['store']]);
    }

    public function index()
    {
        return view('transaksi.kasir');
    }

    public function getuom(Request $request)
    {
        $dataprd = DataProduk::find($request->id);
        $uom = DataUnit::where('aktif', '1')
            ->where('satuan', $dataprd->satuan)
            ->get();
        $response = array();
        foreach ($uom as $data) {
            $response[] = array(
                "id" => $data->satuan,
                "text" => $data->satuan
            );
        }
        return response()->json($response);
    }

    public function listproduk(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->search;
            if ($search == '' || $search == []) {
                $data = DataProduk::where('aktif', 1)->orderby('nama', 'asc')->select('id', 'nama')->limit(10)->get();
            } else {
                $data = DataProduk::where('aktif', 1)->orderby('nama', 'asc')->select('id', 'nama')->where('nama', 'like', '%' . $search . '%')->limit(10)->get();
            }
            $response = array();
            foreach ($data as $data) {
                $response[] = array(
                    "id" => $data->id,
                    "text" => $data->nama
                );
            }
            return response()->json($response);
        }
    }

    public function listtempkasir(Request $request)
    {
        $data = DataTempKasir::select(DB::raw("CONCAT(data_produks.nama,' ',temp_kasirs.ket) as name"), 'temp_kasirs.satuan', 'temp_kasirs.harga', 'temp_kasirs.qty', 'temp_kasirs.id', 'temp_kasirs.amount',)
            ->join('data_produks', 'temp_kasirs.idproduk', '=', 'data_produks.id')
            ->where('temp_kasirs.created_by', Session::get('usernamelogin'))
            ->where('data_produks.aktif', 1)
            ->where('temp_kasirs.aktif', 1)
            ->get();
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($request) {
                $actionBtn = '';
                if ($request->user()->can('kasir-delete')) {
                    $actionBtn = '<a href="javascript:void(0)" id="btndelete" class="delete btn btn-danger btn-xs" data-id="' . $row->id . '">Hapus</a>';
                } else {
                    $actionBtn = '';
                }
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getHarga(Request $request)
    {
        $prdData['data'] = DataProduk::where('aktif', 1)->orderby("nama", "asc")
            ->select('id', 'hargajual', 'hargajualpcs')
            ->where('id', $request->id)
            ->get();
        return response()->json($prdData);
    }

    public function storetemp(Request $request)
    {
        $keterangan = '';
        if ($request->ket == null) {
            $keterangan = '';
        } else {
            $keterangan = $request->ket;
        }

        $created_by = Session::get('usernamelogin');
        $cekdata = DataTempKasir::where('idproduk', $request->nama)
            ->where('satuan', $request->satuan)
            ->where('harga', $request->harga)
            ->where('created_by', $created_by)
            ->where('aktif', 1)
            ->first();
        if ($cekdata != null) {
            $update = DataTempKasir::where('idproduk', $request->nama)
                ->where('satuan', $request->satuan)
                ->where('harga', $request->harga)
                ->where('created_by', $created_by)
                ->where('aktif', 1)
                ->update([
                    'qty' => $cekdata->qty + $request->qty,
                    'amount' => ($cekdata->qty + $request->qty) * $request->harga,
                    'amountbeli' => ($cekdata->qty + $request->qty) * $cekdata->hargabeli,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $created_by,
                    'ket' => $cekdata->ket . ', ' . $keterangan
                ]);
            if ($update) {
                $status = ['title' => 'Sukses!', 'status' => 'success', 'message' => 'Data Berhasil Disimpan'];
                return response()->json($status, 200);
            } else {
                $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Data Gagal Disimpan'];
                return response()->json($status, 200);
            }
        }
        $gethargabeli = DataProduk::where('id', $request->nama)->first();
        if (strtolower($request->satuan) == 'pack') {
            $cekhargabeli = $gethargabeli->hargabelipcs;
        } else {
            $cekhargabeli = $gethargabeli->hargabeli;
        }
        $amount = $request->harga * $request->qty;
        $amountbeli = $cekhargabeli * $request->qty;
        $created_by = Session::get('usernamelogin');
        $savetemp = DataTempKasir::insert([
            'idproduk' => $request->nama,
            'satuan'   => $request->satuan,
            'harga' => $request->harga,
            'hargabeli' => $cekhargabeli,
            'qty'   => $request->qty,
            'amount'   => $amount,
            'amountbeli'   => $amountbeli,
            'aktif'    => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $created_by,
            'ket' => $keterangan
        ]);

        if ($savetemp) {
            $status = ['title' => 'Sukses!', 'status' => 'success', 'message' => 'Data Berhasil Disimpan'];
            return response()->json($status, 200);
        } else {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Data Gagal Disimpan'];
            return response()->json($status, 200);
        }
    }

    public function destroy(Request $request)
    {
        $created_by = Session::get('usernamelogin');
        $delete = DataTempKasir::where('id', $request->id)->update([
            'aktif' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $created_by
        ]);

        if ($delete) {
            $status = ['title' => 'Sukses!', 'status' => 'success', 'message' => 'Data Berhasil Dihapus'];
            return response()->json($status, 200);
        } else {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Data Gagal Dihapus'];
            return response()->json($status, 200);
        }
    }

    public function getamount()
    {
        $sumamount = DataTempKasir::where('aktif', 1)
            ->where('temp_kasirs.created_by', Session::get('usernamelogin'))
            ->selectRaw(' sum(amount) as totamount ')->first();
        return response()->json(['status' => 200, 'data' => $sumamount, 'message' => 'Berhasil']);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $getproduk = DataTempKasir::where('temp_kasirs.aktif', 1)
            ->where('temp_kasirs.created_by', Session::get('usernamelogin'))
            ->get();

        $ambilinv = DataKasir::latest('id')->first();
        if ($ambilinv == '' || $ambilinv == [] || $ambilinv == null) {
            $no = 1;
            $inv = 'INV' . '/' . date('my') . '/'  . sprintf("%05s", abs($no));
        } else {
            $explodeno = explode('/', $ambilinv->noinv);
            $noUrutAkhir = (int)$explodeno[2];
            $bulaninv = $ambilinv->noinv;
            $bulan = Str::substr($bulaninv, 4, 4);
            if ($bulan == date('my')) {
                $inv = 'INV' . '/' . date('my') . '/'  . sprintf("%05s", abs($noUrutAkhir + 1));
            } else {
                $no = 1;
                $inv = 'INV' . '/' . date('my') . '/'  . sprintf("%05s", abs($no));
            }
        }

        $sumamountbeli = DataTempKasir::where('aktif', 1)
            ->where('temp_kasirs.created_by', Session::get('usernamelogin'))
            ->selectRaw(' sum(amountbeli) as ttlamtbeli ')->first();

        $created_by = Session::get('usernamelogin');
        foreach ($getproduk as $key => $val) {
            $insert = DataKasir::create([
                'noinv'   => $inv,
                'idproduk' => $val->idproduk,
                'satuan'   => $val->satuan,
                'harga' => $val->harga,
                'hargabeli' => $val->hargabeli,
                'qty'    => $val->qty,
                'amount'    => $val->amount,
                'amountbeli'    => $val->amountbeli,
                'kepada'    => $request->kepada,
                'totalamount' => $request->subtotal,
                'totalamountbeli' => $sumamountbeli->ttlamtbeli,
                'diskon' => $request->diskon,
                'totalamountdiskon' => $request->subtotal - $request->diskon,
                'dp'        => ($request->cekbokdp == 'true') ? 1 : 0,
                'lunas'     => ($request->cekbokdp == 'false') ? 1 : 0,
                'bayar'     => $request->pembayaran,
                'kurangkembali' => $request->kekurangan,
                'aktif'     => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $created_by,
                'ket'   => $val->ket,
            ]);

            $update = DataTempKasir::where('id', $val->id)->update([
                'aktif' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $created_by
            ]);
        }
        if ($insert && $update) {
            DB::commit();
            $status = ['title' => 'Sukses!', 'status' => 'success', 'message' => $inv . ' ' . 'Berhasil Disimpan'];
            return response()->json($status, 200);
        } else {
            DB::rollback();
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Data Gagal Disimpan'];
            return response()->json($status, 200);
        }
    }

    public function cetak()
    {
        $data = DataKasir::latest('id')
            ->where('Aktif', 1)
            ->where('created_by', Session::get('usernamelogin'))
            ->select('noinv')
            ->first();

        $datacetak = DataKasir::select('data_produks.nama', 'data_kasirs.*', 'trans_lunas.bayar As bayardp', 'trans_lunas.lunas As lunasdp')
            ->join('data_produks', 'data_kasirs.idproduk', 'data_produks.id')
            ->leftjoin('trans_lunas', 'data_kasirs.noinv', 'trans_lunas.noinv')
            ->where('data_kasirs.noinv', $data->noinv)
            ->get();
        return view('report.printstruckrpt')->with('data', $datacetak);
    }

    public function resetdata(Request $request)
    {
        $created_by = Session::get('usernamelogin');

        $cekdata = DataTempKasir::where('created_by', $created_by)->where('aktif', 1)->first();
        if ($cekdata == null) {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Data Kosong'];
            return response()->json($status, 200);
        }
        $reset = DataTempKasir::where('created_by', $created_by)
            ->where('aktif', 1)
            ->update([
                'aktif' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $created_by
            ]);

        if ($reset) {
            $status = ['title' => 'Sukses!', 'status' => 'success', 'message' => 'Transaksi Berhasil Direset'];
            return response()->json($status, 200);
        } else {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Data Gagal Direset'];
            return response()->json($status, 200);
        }
    }
}
