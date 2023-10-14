<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $where = [];
        if ($request->name) {
            $where[] = ['name', 'like', '%' . $request->name . '%'];
        }
        if ($request->email) {
            $where[] = ['email', 'like', '%' . $request->email . '%'];
        }
        $user = User::orderBy('id', 'desc');
        if (!empty($where)) {
            $user = $user->where($where);
        }
        $user = $user->get();
        if ($user->count()) {
            $status = 'success';
        } else {
            $status = 'no_data';
        }
        $response = [
            'status' => $status,
            'data' => $user,
        ];
        return $response;
    }
    public function detail(User $user)
    {
        if (!$user) {
            $status = 'no_data';
        } else {
            $status = 'success';
        }
        $response = [
            'status' => $status,
            'data' => $user,
        ];
        return $response;
    }
    public function create(Request $request)
    {
        
        $this->validation($request);

        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        $user->save();

        if ($user->id) {

            $response = [
                'status' => 'success',
                'data' => $user,
            ];
        } else {
            $response = [
                'status' => 'error',
                'data' => $user,
            ];
        }
    }
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            $response = [
                'status' => 'no_data',
            ];
        } else {

            $this->validation($request,$id);

            $method = $request->method();

            if ($method == 'PUT') {
                $user->name = $request->name;
                $user->email = $request->email;
                if ($request->password) {
                    $user->password = Hash::make($request->password);
                } else {
                    $user->password = null;
                }
                $user->save();
            } else {
                if ($request->name) {
                    $user->name = $request->name;
                }
                if ($request->email) {
                    $user->email = $request->email;
                }
                if ($request->password) {
                    $user->password = Hash::make($request->password);
                }
                $user->save();
            }
            $response = [
                'status' => 'success',
                'data' => $user
            ];
           
        }
        return $response;
    }
    public function delete(User $user)
    {
        return $user;
    }
    public function validation(Request $request,$id=0){
        $rules = [
            'name' => 'required|min:5',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:3'
        ];

        $message = [
            'name.required' => 'Tên bắt buộc phải nhập',
            'name.min' => 'Tên không được nhỏ hơn :min kí tự',
            'email.required' => 'Email bắt buộc phải nhập',
            'email.email' => 'Chưa đúng định dạng email',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Mật khẩu bắt buộc phải nhập',
            'password.min' => 'Mật khẩu không được nhỏ hơn :min kí tự'
        ];
        $request->validate($rules, $message);

    }
    

}
