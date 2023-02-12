<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RoleController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:role-create|role-edit|role-delete|role-print', ['only' => ['index', 'datarole']]);
        $this->middleware('permission:role-create', ['only' => ['store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $permission = Permission::orderby('name', 'asc')->get();
        $userlogin = Session::get('usernamelogin');
        return view('tools.datarole', compact('permission', 'userlogin'));
    }

    public function datarole(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::where('aktif', 1)->orderby('name', 'asc')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($request) {
                    $actionBtn = '';
                    if ($request->user()->can('role-edit') && $request->user()->can('role-delete')) {
                        $actionBtn = '<a href="javascript:void(0)" id="btnshow" class="edit btn btn-success btn-xs" data-id="' . $row->id . '">View</a>';
                        $actionBtn .= '&nbsp;';
                        $actionBtn .= '<a href="javascript:void(0)" id="btnedit" class="edit btn btn-primary btn-xs" data-id="' . $row->id . '">Edit</a>';
                        $actionBtn .= '&nbsp;';
                        $actionBtn .= '<a href="javascript:void(0)" id="btndelete" class="delete btn btn-danger btn-xs" data-id="' . $row->id . '">Hapus</a>';
                    } elseif ($request->user()->can('role-delete')) {
                        $actionBtn = '<a href="javascript:void(0)" id="btnshow" class="edit btn btn-success btn-xs" data-id="' . $row->id . '">View</a>';
                        $actionBtn .= '&nbsp;';
                        $actionBtn .= '<a href="javascript:void(0)" id="btndelete" class="delete btn btn-danger btn-xs" data-id="' . $row->id . '">Hapus</a>';
                    } elseif ($request->user()->can('role-edit')) {
                        $actionBtn = '<a href="javascript:void(0)" id="btnshow" class="edit btn btn-success btn-xs" data-id="' . $row->id . '">View</a>';
                        $actionBtn .= '&nbsp;';
                        $actionBtn .= '<a href="javascript:void(0)" id="btnedit" class="edit btn btn-primary btn-xs" data-id="' . $row->id . '">Edit</a>';
                    } else {
                        $actionBtn = '<a href="javascript:void(0)" id="btnshow" class="edit btn btn-success btn-xs" data-id="' . $row->id . '">View</a>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $perm = json_decode($request->permissions, true);
        $this->validate($request, [
            'name' => 'required',
        ]);

        DB::beginTransaction();
        $cek = Role::where('name', $request->name)->where('aktif', 1)->first();
        if ($cek != null) {
            return redirect()->route('indexrole')->with('failed', 'Role Gagal Disimmpan');
        }
        $role = Role::create([
            'name' => $request->input('name'),
            'aktif' => 1,
            'created_by' => $request->input('created_by')
        ]);
        $syncrole = $role->syncPermissions($perm);

        if ($role && $syncrole) {
            DB::commit();
            $status = ['title' => 'Sukses!', 'status' => 'success', 'message' => 'Data Berhasil Disimpan'];
            return response()->json($status, 200);
        } else {
            DB::rollback();
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'Data Gagal Disimpan'];
            return response()->json($status, 200);
        }
    }

    public function show(Request $request)
    {
        $role = Role::find($request->id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $request->id)
            ->orderby('permissions.name', 'asc')
            ->get();

        return response()->json(['status' => 'success', 'role' => $role, 'rp' => $rolePermissions], 200);
    }

    public function edit(Request $request)
    {
        $role = Role::find($request->id);
        $permission = Permission::orderby('name', 'asc')->get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $request->id)
            ->get();
        return response()->json(['status' => 'success', 'role' => $role, 'permission' => $permission, 'rp' => $rolePermissions], 200);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $perm = json_decode($request->permissions, true);
        $this->validate($request, [
            'name' => 'required',
        ]);
        DB::beginTransaction();
        $role = Role::find($request->id);
        $role->name = $request->input('name');
        $role->updated_by = $request->input('updated_by');
        $update = $role->save();

        $syncperm = $role->syncPermissions($perm);

        if ($update && $syncperm) {
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
        $cekrole = DB::table('model_has_roles')->where('role_id', $request->id)->first();
        if ($cekrole != null) {
            return redirect()->route('indexrole')->with('failed', 'Role Sudah Digunakan');
        }
        $delete = DB::table("roles")->where('id', $request->id)->delete();
        $role = DB::table('role_has_permissions')->where('role_id', $request->id)->first();
        if ($role != null) {
            $deleteroleperm = DB::table('role_has_permissions')->where('role_id', $request->id)->delete();
        } else {
            $deleteroleperm = 1;
        }

        if ($delete && $deleteroleperm) {
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
