<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataUnit;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Session;

class UnitController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:unit-list|unit-create|unit-edit|unit-delete|unit-print', ['only' => ['index', 'listproduk']]);
        $this->middleware('permission:unit-create', ['only' => ['store']]);
        $this->middleware('permission:unit-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:unit-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('master.dataunit');
    }

    public function listunit(Request $request)
    {
        if ($request->ajax()) {
            $data = DataUnit::where('aktif', 1)->orderby('satuan', 'asc')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($request) {
                    $actionBtn = '';
                    if ($request->user()->can('unit-edit') && $request->user()->can('produk-delete')) {
                        $actionBtn = '<a href="javascript:void(0)" id="btnedit" class="edit btn btn-primary btn-xs" data-id="' . $row->id . '">Edit</a>';
                        $actionBtn .= '&nbsp;';
                        $actionBtn .= '<a href="javascript:void(0)" id="btndelete" class="delete btn btn-danger btn-xs" data-id="' . $row->id . '">Hapus</a>';
                    } elseif ($request->user()->can('unit-delete')) {
                        $actionBtn = '<a href="javascript:void(0)" id="btndelete" class="delete btn btn-danger btn-xs" data-id="' . $row->id . '">Hapus</a>';
                    } elseif ($request->user()->can('unit-edit')) {
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
        $ceknama = DataUnit::where('satuan', $request->satuan)->first();
        if ($ceknama != null) {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Satuan Sudah Tersedia'];
            return response()->json($status, 200);
        }
        $created_by = Session::get('usernamelogin');
        $save = DataUnit::insert([
            'satuan'        => $request->satuan,
            'qty'        => $request->qty,
            'aktif' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $created_by
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
        $data = DataUnit::find($request->id);
        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    public function update(Request $request)
    {
        $updatedby = Session::get('usernamelogin');
        $data = DataUnit::find($request->id);
        $ceknama = DataUnit::where('satuan', $request->satuan)->where('aktif', 1)->first();
        if ($ceknama != null && $request->satuan != $data->satuan) {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Satuan Sudah Tersedia'];
            return response()->json($status, 200);
        }
        $update = DataUnit::where('id', $request->id)->update([
            'satuan' => $request->satuan,
            'qty'        => $request->qty,
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
        $delete = DataUnit::where('id', $request->id)->update([
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
