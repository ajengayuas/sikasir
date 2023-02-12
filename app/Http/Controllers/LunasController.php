<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransLunas;
use Illuminate\Support\Facades\DB;
use App\Models\DataTempKasir;
use App\Models\DataKasir;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Session;

class LunasController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:lunas-list|lunas-create|lunas-edit|lunas-delete|lunas-print', ['only' => ['index', 'listprodukdp']]);
        $this->middleware('permission:lunas-print', ['only' => ['cetak']]);
        $this->middleware('permission:lunas-create', ['only' => ['store']]);
    }

    public function index()
    {
        return view('transaksi.pelunasan');
    }

    public function listinv(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->search;
            if ($search == '' || $search == []) {
                $data = DataKasir::select('data_kasirs.noinv')
                    ->leftjoin('trans_lunas', 'data_kasirs.noinv', '=', 'trans_lunas.noinv')
                    ->where('data_kasirs.dp', 1)
                    ->where('data_kasirs.aktif', 1)
                    ->where(function ($query) {
                        $query->whereNull('trans_lunas.lunas')
                            ->orWhere('trans_lunas.lunas', 0)
                            ->orWhere('trans_lunas.aktif', 0);
                    })
                    ->groupBy('data_kasirs.noinv')
                    ->orderBy('data_kasirs.noinv', 'desc')
                    ->limit(10)
                    ->get();
            } else {
                $data = DataKasir::select('data_kasirs.noinv')
                    ->leftjoin('trans_lunas', 'data_kasirs.noinv', '=', 'trans_lunas.noinv')
                    ->where('data_kasirs.dp', 1)
                    ->where('data_kasirs.aktif', 1)
                    ->where(function ($query) {
                        $query->whereNull('trans_lunas.lunas')
                            ->orWhere('trans_lunas.lunas', 0)
                            ->orWhere('trans_lunas.aktif', 0);
                    })
                    ->where('data_kasirs.noinv', 'like', '%' . $search . '%')
                    ->groupBy('data_kasirs.noinv')
                    ->orderBy('data_kasirs.noinv', 'desc')
                    ->limit(10)
                    ->get();
            }
            $response = array();
            foreach ($data as $data) {
                $response[] = array(
                    "id" => $data->noinv,
                    "text" => $data->noinv
                );
            }
            return response()->json($response);
        }
    }

    public function listprodukdp(Request $request)
    {

        $data = DataKasir::select('data_kasirs.*', 'data_produks.nama', 'trans_lunas.bayar As bayardp', 'trans_lunas.aktif As aktiflunas')
            ->join('data_produks', 'data_kasirs.idproduk', '=', 'data_produks.id')
            ->leftjoin('trans_lunas', 'data_kasirs.noinv', '=', 'trans_lunas.noinv')
            ->where('data_kasirs.dp', 1)
            ->where('data_kasirs.aktif', 1)
            ->where('data_kasirs.noinv', $request->noinv)
            ->where(function ($query) {
                $query->whereNull('trans_lunas.lunas')
                    ->orWhere('trans_lunas.lunas', 0)
                    ->orWhere('trans_lunas.aktif', 0);
            })
            ->get();
        return Datatables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function listprodukdp2($noinv = null, Request $request)
    {

        $data = DataKasir::select('data_kasirs.*', 'data_produks.nama', 'trans_lunas.bayar As bayardp', 'trans_lunas.aktif As aktiflunas')
            ->join('data_produks', 'data_kasirs.idproduk', '=', 'data_produks.id')
            ->leftjoin('trans_lunas', 'data_kasirs.noinv', '=', 'trans_lunas.noinv')
            ->where('data_kasirs.dp', 1)
            ->where('data_kasirs.aktif', 1)
            ->where('data_kasirs.noinv', $request->noinv)
            ->where(function ($query) {
                $query->whereNull('trans_lunas.lunas')
                    ->orWhere('trans_lunas.lunas', 0)
                    ->orWhere('trans_lunas.aktif', 0);
            })
            ->get();
        return Datatables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function store(Request $request)
    {
        if ($request->kurang == $request->bayar || $request->bayar > $request->kurang) {
            $lunas = 1;
        } else {
            $lunas = 0;
        }
        $created_by = Session::get('usernamelogin');
        if ($request->hisbayar > 0) {
            DB::beginTransaction();
            $update = TransLunas::where('noinv', $request->noinv)->update([
                'bayar' => $request->hisbayar + $request->bayar,
                'lunas' => $lunas,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $created_by
            ]);
            $updatekasir = DataKasir::where('noinv', $request->noinv)->update([
                'lunas' => $lunas,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $created_by
            ]);
            if ($update && $updatekasir) {
                DB::commit();
                $status = ['title' => 'Sukses!', 'status' => 'success', 'message' => $request->noinv . ' ' . 'Berhasil Disimpan'];
                return response()->json($status, 200);
            } else {
                DB::rollback();
                $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Data Gagal Disimpan'];
                return response()->json($status, 200);
            }
        } else {
            DB::beginTransaction();
            $insert = TransLunas::create([
                'noinv'   => $request->noinv,
                'kurang' => $request->kurang,
                'bayar'   => $request->bayar,
                'lunas' => $lunas,
                'aktif'    => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by' => $created_by,
                'updated_by' => $created_by
            ]);
            $updatekasir = DataKasir::where('noinv', $request->noinv)->update([
                'lunas' => $lunas,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $created_by
            ]);
            if ($insert && $updatekasir) {
                DB::commit();
                $status = ['title' => 'Sukses!', 'status' => 'success', 'message' => $request->noinv . ' ' . 'Berhasil Disimpan'];
                return response()->json($status, 200);
            } else {
                DB::rollback();
                $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Data Gagal Disimpan'];
                return response()->json($status, 200);
            }
        }
    }

    public function cetak()
    {
        $data = TransLunas::latest('updated_at')
            ->where('Aktif', 1)
            ->where('updated_by', Session::get('usernamelogin'))
            ->select('noinv')
            ->first();
        $datacetak = DataKasir::select('data_produks.nama', 'data_kasirs.*', 'trans_lunas.bayar As bayardp', 'trans_lunas.lunas As lunasdp')
            ->join('data_produks', 'data_kasirs.idproduk', 'data_produks.id')
            ->leftjoin('trans_lunas', 'data_kasirs.noinv', 'trans_lunas.noinv')
            ->where('data_kasirs.noinv', $data->noinv)
            ->get();
        return view('report.printstruckrpt')->with('data', $datacetak);
    }
}
