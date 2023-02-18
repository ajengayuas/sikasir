<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataKasir;
use App\Models\DataProduk;
use App\Models\TransLunas;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;

class ReportController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:jual-list|jual-create|jual-edit|jual-delete|jual-print', ['only' => ['index2', 'detailjual2']]);
        $this->middleware('permission:kasir-delete', ['only' => ['destroyinv']]);
        $this->middleware('permission:listjual-list|listjual-create|listjual-edit|listjual-delete|listjual-print', ['only' => ['index', 'listjual']]);
        $this->middleware('permission:detjual-list|detjual-create|detjual-edit|detjual-delete|listjual-print', ['only' => ['detail', 'detailjual']]);
        $this->middleware('permission:detjual-print', ['only' => ['cetak']]);
        $this->middleware('permission:uang-list|uang-create|uang-edit|uang-delete|uang-print', ['only' => ['viewdatakeu', 'listdatakeu']]);
        $this->middleware('permission:kredit-list|kredit-create|kredit-edit|kredit-delete|kredit-print', ['only' => ['viewkredit', 'detailkredit']]);
    }
    public function index()
    {
        return view('report.rptpenjualan');
    }

    public function listjual(Request $request)
    {
        if ($request->ajax()) {
            $data = DataKasir::select(DB::raw("DATE_FORMAT(data_kasirs.created_at, '%d-%m-%Y') as at,CONCAT(data_produks.nama,' ',data_kasirs.ket) as name"), 'data_kasirs.noinv', 'data_kasirs.qty', 'data_kasirs.satuan', 'data_kasirs.harga', 'data_kasirs.amount')
                ->join('data_produks', 'data_kasirs.idproduk', 'data_produks.id')
                ->where('data_kasirs.aktif', 1)
                ->whereBetween('data_kasirs.created_at', [Carbon::parse($request->date1)->startOfDay(), Carbon::parse($request->date2)->endOfDay()])
                ->orderby('data_kasirs.created_at', 'asc')
                ->orderby('data_kasirs.noinv', 'asc')
                ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function detail()
    {
        return view('report.rptdetailpenjualan');
    }

    public function detailjual(Request $request)
    {
        if ($request->ajax()) {
            $data = DataKasir::select(DB::raw('DATE_FORMAT(data_kasirs.created_at, "%d-%m-%Y") as at'), 'data_kasirs.noinv', 'data_kasirs.kepada', 'data_kasirs.totalamountdiskon', 'data_kasirs.dp', 'data_kasirs.lunas', 'data_kasirs.created_at')
                ->where('data_kasirs.aktif', 1)
                ->whereBetween('data_kasirs.created_at', [Carbon::parse($request->date1)->startOfDay(), Carbon::parse($request->date2)->endOfDay()])
                ->orderby('data_kasirs.created_at', 'asc')
                ->distinct()
                ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($request) {
                    $actionBtn = '';
                    if ($request->user()->can('detjual-print')) {
                        $actionBtn = '<a href="javascript:void(0)" id="btndetail" class="edit btn btn-primary btn-xs" data-id="' . $row->noinv . '">Detail</a> 
                    <a href="#" id="btnprint" class="edit btn btn-primary btn-xs" data-id="' . $row->noinv . '">Print</a>';
                    } else {
                        $actionBtn = '<a href="javascript:void(0)" id="btndetail" class="edit btn btn-primary btn-xs" data-id="' . $row->noinv . '">Detail</a> ';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function detailjual2(Request $request)
    {
        if ($request->ajax()) {
            $data = DataKasir::select(DB::raw('DATE_FORMAT(data_kasirs.created_at, "%d-%m-%Y") as at'), 'data_kasirs.noinv', 'data_kasirs.kepada', 'data_kasirs.totalamountdiskon', 'data_kasirs.dp', 'data_kasirs.lunas', 'data_kasirs.created_at')
                ->where('data_kasirs.aktif', 1)
                ->whereBetween('data_kasirs.created_at', [Carbon::parse($request->date1)->startOfDay(), Carbon::parse($request->date2)->endOfDay()])
                ->orderby('data_kasirs.created_at', 'asc')
                ->distinct()
                ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($request) {
                    $actionBtn = '';
                    if ($request->user()->can('jual-delete')) {
                        $actionBtn = '<a href="javascript:void(0)" id="btndetail" class="edit btn btn-primary btn-xs" data-id="' . $row->noinv . '">Detail</a> 
                    <a href="#" id="btndelete" class="edit btn btn-primary btn-xs" data-id="' . $row->noinv . '">Hapus</a> ';
                    } else {
                        $actionBtn = '<a href="javascript:void(0)" id="btndetail" class="edit btn btn-primary btn-xs" data-id="' . $row->noinv . '">Detail</a>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function viewdetailjual(Request $request)
    {
        if ($request->ajax()) {
            $data = DataKasir::select(DB::raw("DATE_FORMAT(data_kasirs.created_at, '%d %M %Y %h:%m:%d') as at,CONCAT(data_produks.nama,' ',data_kasirs.ket) as name"), 'data_kasirs.noinv', 'data_kasirs.kepada', 'data_kasirs.dp', 'data_kasirs.lunas', 'data_kasirs.totalamount', 'data_kasirs.diskon', 'data_kasirs.totalamountdiskon', 'data_kasirs.bayar', 'data_kasirs.qty', 'data_kasirs.satuan', 'data_kasirs.amount', 'data_kasirs.harga', 'trans_lunas.bayar as bayarlunas')
                ->join('data_produks', 'data_kasirs.idproduk', 'data_produks.id')
                ->leftjoin('trans_lunas', 'data_kasirs.noinv', 'trans_lunas.noinv')
                ->where('data_kasirs.noinv', $request->inv)
                ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function cetak($noinv)
    {
        $decryptinv = base64_decode($noinv);
        $datacetak = DataKasir::select('data_produks.nama', 'data_kasirs.*', 'trans_lunas.bayar As bayardp', 'trans_lunas.lunas As lunasdp')
            ->join('data_produks', 'data_kasirs.idproduk', 'data_produks.id')
            ->leftjoin('trans_lunas', 'data_kasirs.noinv', 'trans_lunas.noinv')
            ->where('data_kasirs.noinv', $decryptinv)
            ->get();
        return view('report.printstruckrpt')->with('data', $datacetak);
    }

    public function viewdatakeu()
    {
        return view('report.rptkeuangan');
    }

    public function listdatakeu(Request $request)
    {
        if ($request->ajax()) {
            $data = DataKasir::select(DB::raw('DATE_FORMAT(data_kasirs.created_at, "%d-%m-%Y") as at'), 'data_kasirs.noinv', 'data_kasirs.totalamountdiskon', 'data_kasirs.totalamountbeli', 'data_kasirs.lunas', 'data_kasirs.created_at')
                ->where('data_kasirs.aktif', 1)
                ->where('data_kasirs.lunas', 1)
                ->whereBetween('data_kasirs.created_at', [Carbon::parse($request->date1)->startOfDay(), Carbon::parse($request->date2)->endOfDay()])
                ->orderby('data_kasirs.created_at', 'asc')
                ->distinct()
                ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function getamount(Request $request)
    {

        $partsSubquery = DataKasir::select('data_kasirs.noinv', 'data_kasirs.totalamountbeli', 'data_kasirs.totalamountdiskon')
            ->where('data_kasirs.aktif', 1)
            ->where('data_kasirs.lunas', 1)
            ->whereBetween('data_kasirs.created_at', [Carbon::parse($request->date1)->startOfDay(), Carbon::parse($request->date2)->endOfDay()])
            ->distinct();

        $data = DataKasir::select(DB::raw('sum(totalamountbeli) as totalamountbelisum, sum(totalamountdiskon) as totalamountdiskonsum'))
            ->fromSub($partsSubquery, 'PE')
            ->get();

        foreach ($data as $key => $val) {
            $amtjual = $val->totalamountdiskonsum;
            $amtbeli = $val->totalamountbelisum;
        }
        if ($amtjual >= $amtbeli) {
            $ket = 'Laba';
            $hasil = $amtjual - $amtbeli;
        } else {
            $ket = 'Rugi';
            $hasil = $amtbeli - $amtjual;
        }
        return response()->json(['status' => 200, 'ket' => $ket, 'hasil' => $hasil, 'jual' => $amtjual, 'beli' => $amtbeli, 'message' => 'Berhasil']);
    }

    public function viewkredit()
    {
        return view('report.rptkredit');
    }

    public function detailkredit(Request $request)
    {
        if ($request->ajax()) {
            $data = DataKasir::select(DB::raw('DATE_FORMAT(data_kasirs.created_at, "%d-%m-%Y") as at, data_kasirs.noinv, data_kasirs.kepada, data_kasirs.totalamountdiskon, data_kasirs.bayar, trans_lunas.bayar as bayardp, data_kasirs.totalamountdiskon - data_kasirs.bayar - IFNULL(trans_lunas.bayar, 0) as kurang'), 'data_kasirs.created_at')
                ->leftjoin('trans_lunas', 'data_kasirs.noinv', 'trans_lunas.noinv')
                ->whereBetween('data_kasirs.created_at', [Carbon::parse($request->date1)->startOfDay(), Carbon::parse($request->date2)->endOfDay()])
                ->where('data_kasirs.dp', 1)
                ->where('data_kasirs.lunas', 0)
                ->where('data_kasirs.aktif', 1)
                ->orderby('data_kasirs.created_at', 'asc')
                ->distinct()
                ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="javascript:void(0)" id="btndetail" class="edit btn btn-primary btn-xs" data-id="' . $row->noinv . '">Detail</a> ';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function destroyinv(Request $request)
    {
        DB::beginTransaction();
        $updatedby = Session::get('usernamelogin');
        $delete = DataKasir::where('noinv', $request->noinv)->update([
            'aktif' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $updatedby
        ]);
        $cekinv = TransLunas::where('noinv', $request->noinv)->first();
        $delete2 = 0;
        if ($cekinv != null) {
            $delete2 = TransLunas::where('noinv', $request->noinv)->update([
                'aktif' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $updatedby
            ]);
        } else {
            $delete2 = 1;
        }
        if ($delete && $delete2) {
            DB::commit();
            $status = ['title' => 'Sukses!', 'status' => 'success', 'message' => 'Data Berhasil Dihapus'];
            return response()->json($status, 200);
        } else {
            DB::rollback();
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Data Gagal Dihapus'];
            return response()->json($status, 200);
        }
    }

    public function index2()
    {
        return view('transaksi.datapenjualan');
    }

    public function viewproduk()
    {
        return view('report.rptproduk');
    }

    public function daftarharga()
    {
        $data = DataProduk::where('aktif', 1)
            ->orderby('nama', 'asc')
            ->get();
        return Datatables::of($data)
            ->addIndexColumn()
            ->make(true);
    }
}
