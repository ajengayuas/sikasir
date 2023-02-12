<?php

namespace App\Http\Controllers;

use App\Models\DataKasir;
use Illuminate\Http\Request;
use App\Models\DataProduk;
use App\Models\DataTempKasir;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Session;

class ProdukController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:produk-list|produk-create|produk-edit|produk-delete|produk-print', ['only' => ['index', 'listproduk']]);
        $this->middleware('permission:produk-create', ['only' => ['store']]);
        $this->middleware('permission:produk-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:produk-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('master.dataproduk');
    }

    public function listproduk(Request $request)
    {
        if ($request->ajax()) {
            $data = DataProduk::where('aktif', 1)->orderby('kode', 'desc')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($request) {
                    $actionBtn = '';
                    if ($request->user()->can('produk-edit') && $request->user()->can('produk-delete')) {
                        $actionBtn = '<a href="javascript:void(0)" id="btnedit" class="edit btn btn-primary btn-xs" data-id="' . $row->id . '">Edit</a>';
                        $actionBtn .= '&nbsp;';
                        $actionBtn .= '<a href="javascript:void(0)" id="btndelete" class="delete btn btn-danger btn-xs" data-id="' . $row->id . '">Hapus</a>';
                    } elseif ($request->user()->can('produk-delete')) {
                        $actionBtn = '<a href="javascript:void(0)" id="btndelete" class="delete btn btn-danger btn-xs" data-id="' . $row->id . '">Hapus</a>';
                    } elseif ($request->user()->can('produk-edit')) {
                        $actionBtn = '<a href="javascript:void(0)" id="btnedit" class="edit btn btn-primary btn-xs" data-id="' . $row->id . '">Edit</a>';
                    } else {
                        $actionBtn = '';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    public function store(Request $request)
    {
        $ceknama = DataProduk::where('nama', $request->nama)->first();
        if ($ceknama != null) {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Nama Produk Sudah Tersedia'];
            return response()->json($status, 200);
        }
        $updatedby = Session::get('usernamelogin');
        $save = DataProduk::create([
            'kode'        => 'kode',
            'nama'        => $request->nama,
            'qtypcs'      => $request->qtypcs,
            'hargabeli'   => $request->hargabeli,
            'hargabelipcs' => $request->hargabelipcs,
            'hargajual'   => $request->hargajual,
            'hargajualpcs' => $request->hargajualpcs,
            'aktif' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $updatedby
        ]);
        if ($save) {
            $status = ['title' => 'Sukses!', 'status' => 'success', 'message' => 'Data Berhasil Disimpan'];
            return response()->json($status, 200);
        } else {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Data Gagal Disimpan'];
            return response()->json($status, 200);
        }
    }

    public function edit(Request $request)
    {
        $dataprod = DataProduk::find($request->id);
        return response()->json(['status' => 'success', 'data' => $dataprod], 200);
    }

    public function update(Request $request)
    {
        $updatedby = Session::get('usernamelogin');
        $dataprod = DataProduk::find($request->id);
        $ceknama = DataProduk::where('nama', $request->nama)->where('aktif', 1)->first();
        if ($ceknama != null && $request->nama != $dataprod->nama) {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Nama Sudah Tersedia'];
            return response()->json($status, 200);
        }
        $update = DataProduk::where('id', $request->id)->update([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'qtypcs' => $request->qtypcs,
            'hargabeli' => $request->hargabeli,
            'hargabelipcs' => $request->hargabelipcs,
            'hargajual' => $request->hargajual,
            'hargajualpcs' => $request->hargajualpcs,
            'aktif' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $updatedby
        ]);

        if ($update) {
            $status = ['title' => 'Sukses!', 'status' => 'success', 'message' => 'Data Berhasil Diedit'];
            return response()->json($status, 200);
        } else {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Data Gagal Diedit'];
            return response()->json($status, 200);
        }
    }

    public function destroy(Request $request)
    {
        $updatedby = Session::get('usernamelogin');
        $cekbarang = DataTempKasir::where('idproduk', $request->id)->where('aktif', 1)->first();
        $cekbarang2 = DataKasir::where('idproduk', $request->id)->where('aktif', 1)->first();
        if ($cekbarang != null || $cekbarang2 != null) {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Produk sudah digunakan, tidak bisa dihapus'];
            return response()->json($status, 200);
        }
        $delete = DataProduk::where('id', $request->id)->update([
            'aktif' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $updatedby
        ]);

        if ($delete) {
            $status = ['title' => 'Sukses!', 'status' => 'success', 'message' => 'Data Berhasil Dihapus'];
            return response()->json($status, 200);
        } else {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Data Gagal Dihapus'];
            return response()->json($status, 200);
        }
    }
}
