<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use App\Models\Permissions;
use Yajra\Datatables\Datatables;

class MenuController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:menu-list|menu-create|menu-edit|menu-delete|menu-print', ['only' => ['index', 'datamenu']]);
        $this->middleware('permission:menu-create', ['only' => ['store']]);
        $this->middleware('permission:menu-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:menu-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('tools.datamenu');
    }

    public function datamenu(Request $request)
    {
        if ($request->ajax()) {
            $data = Menu::where('aktif', 1)->orderby('mnname', 'asc')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($request) {
                    $actionBtn = '';
                    if ($request->user()->can('menu-edit') && $request->user()->can('menu-delete')) {
                        $actionBtn = '<a href="javascript:void(0)" id="btnedit" class="edit btn btn-primary btn-xs" data-id="' . $row->id . '">Edit</a>';
                        $actionBtn .= '&nbsp;';
                        $actionBtn .= '<a href="javascript:void(0)" id="btndelete" class="delete btn btn-danger btn-xs" data-id="' . $row->id . '">Hapus</a>';
                    } elseif ($request->user()->can('menu-delete')) {
                        $actionBtn = '<a href="javascript:void(0)" id="btndelete" class="delete btn btn-danger btn-xs" data-id="' . $row->id . '">Hapus</a>';
                    } elseif ($request->user()->can('menu-edit')) {
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
        request()->validate([
            'mnname' => 'required',
            'alias' => 'required',
        ]);
        $created_by = $request->user()->email;
        $ceknama = Menu::where('mnname', $request->mnname)->where('aktif', 1)->first();
        if ($ceknama != null) {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Nama Sudah Tersedia'];
            return response()->json($status, 200);
        }
        $cek = Menu::where('alias', strtolower($request->alias))->where('aktif', 1)->first();
        if ($cek != null) {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Alias Sudah Tersedia'];
            return response()->json($status, 200);
        }
        DB::beginTransaction();
        $savemenu = Menu::insert([
            'mnname' => $request->mnname,
            'alias'   => strtolower($request->alias),
            'aktif'    => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $created_by
        ]);
        $nmperm1 = strtolower($request->alias) . '-list';
        $nmperm2 = strtolower($request->alias) . '-create';
        $nmperm3 = strtolower($request->alias) . '-edit';
        $nmperm4 = strtolower($request->alias) . '-delete';
        $nmperm5 = strtolower($request->alias) . '-print';
        $savepermission1 = Permissions::insert([
            'name' => $nmperm1,
            'guard_name'   => 'web',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $created_by
        ]);
        $savepermission2 = Permissions::insert([
            'name' => $nmperm2,
            'guard_name'   => 'web',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $created_by
        ]);
        $savepermission3 = Permissions::insert([
            'name' => $nmperm3,
            'guard_name'   => 'web',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $created_by
        ]);
        $savepermission4 = Permissions::insert([
            'name' => $nmperm4,
            'guard_name'   => 'web',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $created_by
        ]);
        $savepermission5 = Permissions::insert([
            'name' => $nmperm5,
            'guard_name'   => 'web',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $created_by
        ]);
        if ($savemenu && $savepermission1 && $savepermission2 && $savepermission3 && $savepermission4 && $savepermission5) {
            DB::commit();
            $status = ['title' => 'Sukses!', 'status' => 'success', 'message' => 'Data Berhasil Disimpan'];
            return response()->json($status, 200);
        } else {
            DB::rollback();
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Data Gagal Disimpan'];
            return response()->json($status, 200);
        }
    }

    public function edit(Request $request)
    {
        $data = Menu::find($request->id);
        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    public function update(Request $request)
    {
        request()->validate([
            'mnname' => 'required',
            'alias' => 'required',
        ]);

        $created_by = $request->user()->email;
        DB::beginTransaction();
        $ceknama = Menu::where('mnname', $request->mnname)->where('aktif', 1)->first();

        if ($ceknama != null && $ceknama->mnname != $request->mnname) {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Nama Sudah Tersedia'];
            return response()->json($status, 200);
        }
        $cek = Menu::where('alias', strtolower($request->alias))->where('aktif', 1)->first();
        if ($cek != null && $cek->alias != strtolower($request->alias)) {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Alias Sudah Tersedia'];
            return response()->json($status, 200);
        }
        $ceknama = Menu::where('id', $request->id)->where('aktif', 1)->first();
        $perm1 = strtolower($ceknama->alias) . '-list';
        $perm2 = strtolower($ceknama->alias) . '-create';
        $perm3 = strtolower($ceknama->alias) . '-edit';
        $perm4 = strtolower($ceknama->alias) . '-delete';
        $perm5 = strtolower($ceknama->alias) . '-print';
        $cekperm = Permissions::select('permissions.*', 'role_has_permissions.*')
            ->join('role_has_permissions', 'permissions.id', 'role_has_permissions.permission_id')
            ->whereIn('permissions.name', array($perm1, $perm2, $perm3, $perm4, $perm5))
            ->first();
        $getalias = Menu::where('id', $request->id)->where('aktif', 1)->first();
        if ($cekperm && $getalias->alias != strtolower($request->alias)) {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Menu Sudah Dipakai'];
            return response()->json($status, 200);
        }
        $savepermission1 = false;
        $savepermission2 = false;
        $savepermission3 = false;
        $savepermission4 = false;
        $savepermission5 = false;
        $deleteperm = false;
        if ($getalias->alias != strtolower($request->alias)) {
            $deleteperm = Permissions::whereIn('name', array($perm1, $perm2, $perm3, $perm4, $perm5))->delete();
            $nmperm1 = strtolower($request->alias) . '-list';
            $nmperm2 = strtolower($request->alias) . '-create';
            $nmperm3 = strtolower($request->alias) . '-edit';
            $nmperm4 = strtolower($request->alias) . '-delete';
            $nmperm5 = strtolower($request->alias) . '-print';
            $savepermission1 = Permissions::insert([
                'name' => $nmperm1,
                'guard_name'   => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $created_by
            ]);
            $savepermission2 = Permissions::insert([
                'name' => $nmperm2,
                'guard_name'   => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $created_by
            ]);
            $savepermission3 = Permissions::insert([
                'name' => $nmperm3,
                'guard_name'   => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $created_by
            ]);
            $savepermission4 = Permissions::insert([
                'name' => $nmperm4,
                'guard_name'   => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $created_by
            ]);
            $savepermission5 = Permissions::insert([
                'name' => $nmperm5,
                'guard_name'   => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $created_by
            ]);
        } else {
            $savepermission1 = true;
            $savepermission2 = true;
            $savepermission3 = true;
            $savepermission4 = true;
            $savepermission5 = true;
            $deleteperm = true;
        }

        $updatemenu = Menu::where('id', $request->id)->update([
            'mnname' => $request->mnname,
            'alias' => strtolower($request->alias),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $created_by
        ]);
        if ($updatemenu && $savepermission1 && $savepermission2 && $savepermission3 && $savepermission4 && $savepermission5 && $deleteperm) {
            DB::commit();
            $status = ['title' => 'Sukses!', 'status' => 'success', 'message' => 'Data Berhasil Diupdate'];
            return response()->json($status, 200);
        } else {
            DB::rollback();
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Data Gagal Diupdate'];
            return response()->json($status, 200);
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        $updatedby = $request->user()->email;
        $ceknama = Menu::where('id', $request->id)->where('aktif', 1)->first();
        $perm1 = strtolower($ceknama->alias) . '-list';
        $perm2 = strtolower($ceknama->alias) . '-create';
        $perm3 = strtolower($ceknama->alias) . '-edit';
        $perm4 = strtolower($ceknama->alias) . '-delete';
        $perm5 = strtolower($ceknama->alias) . '-print';
        $cekperm = Permissions::select('permissions.*', 'role_has_permissions.*')
            ->join('role_has_permissions', 'permissions.id', 'role_has_permissions.permission_id')
            ->whereIn('permissions.name', array($perm1, $perm2, $perm3, $perm4, $perm5))
            ->first();
        if ($cekperm) {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Menu Sudah Dipakai'];
            return response()->json($status, 200);
        }
        $delete = Menu::where('id', $request->id)->update([
            'aktif' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $updatedby
        ]);
        $deleteperm = Permissions::whereIn('name', array($perm1, $perm2, $perm3, $perm4, $perm5))->delete();

        if ($delete && $deleteperm) {
            DB::commit();
            $status = ['title' => 'Sukses!', 'status' => 'success', 'message' => 'Data Berhasil Dihapus'];
            return response()->json($status, 200);
        } else {
            DB::rollback();
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Data Gagal Dihapus'];
            return response()->json($status, 200);
        }
    }
}
