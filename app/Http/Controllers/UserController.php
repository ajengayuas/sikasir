<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:user-list|user-create|user-edit|user-delete|user-print', ['only' => ['index', 'datamenu']]);
        $this->middleware('permission:user-create', ['only' => ['store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $roles = Role::pluck('name', 'name')->all();
        $userRole = '';
        $userlogin = Session::get('usernamelogin');
        return view('tools.datauser', compact('roles', 'userRole', 'userlogin'));
    }

    public function datauser(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('users.*', 'roles.name as nmrole')
                ->leftjoin('model_has_roles', 'users.id', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', 'roles.id')
                ->where('users.aktif', 1)->orderby('users.name', 'asc')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($request) {
                    $actionBtn = '';
                    if ($request->user()->can('user-edit') && $request->user()->can('user-delete')) {
                        $actionBtn = '<a href="javascript:void(0)" id="btnedit" class="edit btn btn-primary btn-xs" data-id="' . $row->id . '">Edit</a>';
                        $actionBtn .= '&nbsp;';
                        $actionBtn .= '<a href="javascript:void(0)" id="btndelete" class="delete btn btn-danger btn-xs" data-id="' . $row->id . '">Hapus</a>';
                    } elseif ($request->user()->can('user-delete')) {
                        $actionBtn = '<a href="javascript:void(0)" id="btndelete" class="delete btn btn-danger btn-xs" data-id="' . $row->id . '">Hapus</a>';
                    } elseif ($request->user()->can('user-edit')) {
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
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);
        DB::beginTransaction();

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['updated_by'] = '';
        $user = User::create($input);

        $saverole = $user->assignRole($request->input('roles'));

        if ($user && $saverole) {
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
        $data = User::select('users.*', 'roles.name as nmrole')
            ->leftjoin('model_has_roles', 'users.id', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', 'roles.id')
            ->where('users.aktif', 1)
            ->where('users.id', $request->id)
            ->first();
        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $request->id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }
        $user = User::find($request->id);
        $input['created_by'] = $user->created_by;
        $updateuser = $user->update($input);
        $deleterole = DB::table('model_has_roles')->where('model_id', $request->id)->delete();

        $addrole = $user->assignRole($request->input('roles'));

        if ($updateuser && $deleterole && $addrole) {
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
        $cek = User::where('id', $request->id)->where('aktif', 1)->first();
        if ($cek->email == $request->user()->email) {
            $status = ['title' => 'Gagal!', 'status' => 'error', 'message' => 'User sedang digunakan'];
            return response()->json($status, 200);
        }
        $delete = User::where('id', $request->id)->update([
            'aktif' => 0,
            'updated_by' => $updatedby
        ]);
        $deleterole = DB::table('model_has_roles')->where('model_id', $request->id)->delete();
        if ($delete && $deleterole) {
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
