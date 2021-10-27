<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class MsUsersController extends Controller
{
    public function editorUsers(Request $request)
    {
        $users = User::find($request->user_id);
        return view('ms_user.editor', compact('users'));
    }

    public function index()
    {
        return view('ms_user.index');
    }

    public function getUsers()
    {
        // dd(auth()->user()->id);
        $users = User::orderBy('id', 'DESC')->get();
        return DataTables::of($users)
        ->addIndexColumn()
        ->addColumn('action', function ($users) {
            $action = '<div class="btn-group" role="group" aria-label="Basic example">  <button class="btn btn-primary btn-sm" data-id="'.$users['id'].'" id="edit" title="Edit"><i class="fas fa-edit"></i></button>';

            if (Auth::id() == $users['id']) {
                $action .= '<button class="btn btn-danger btn-sm" data-id="'.$users['id'].'" id="delete" title="Delete" disabled><i class="fas fa-trash"></i></button>';
            } else {
                $action .= '<button class="btn btn-danger btn-sm" data-id="'.$users['id'].'" id="delete" title="Delete"><i class="fas fa-trash"></i></button>  </div>';
            }
            return $action;
        })
        ->rawColumns(['DT_Row_Index', 'action'])
        ->make(true);
    }

    public function sendToDB(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email:rfc,dns', 'unique:users,email'],
            'password' => ['required', Rules\Password::defaults()],
            // 'employee_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $user = new User();
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $query = $user->save();
            if (!$query) {
                return response()->json(['code' => 0, 'msg' => 'Something went wrong']);
            } else {
                return response()->json(['code' => 1, 'msg' => 'New User has been successfuly saved']);
            }
        }
    }

    public function editUser(Request $request)
    {
        $user_id = $request->user_id;
        $userDetails = User::find($user_id);
        return response()->json(['details' => $userDetails]);
    }

    public function updateUser(Request $request, $user_id)
    {
        $user_id = $request->id;
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email:rfc,dns'],
            // 'employee_id' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $user = User::find($user_id);
            $user->email = $request->email;
            $query = $user->save();
            if ($query) {
                return response()->json(['code' => 1, 'msg' => 'User Has Been Updated']);
            } else {
                return response()->json(['code' => 0, 'msg' => 'Something went wrong']);
            }
        }
    }

    public function deleteUser(Request $request)
    {
        $user_id = $request->user_id;
        $query = User::find($user_id)->delete();

        if($query) {
            return response()->json(['code' => 1, 'msg' => 'User Has Been Deleted From Database']);
        } else {
            return response()->json(['code' => 0, 'msg' => 'Something went wrong']);
        }
    }
}
