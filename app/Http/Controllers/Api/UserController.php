<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return 'All Users';
    }
    public function detail(User $user)
    {
        return 'User ' . $user;
    }
    public function create(Request $request)
    {
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

        $user = new User();

        $user->name =$request->name;
        $user->email=$request->email;
        $user->password = Hash::make($request->password);

        $user->save();

    if ($user->id) {
          
        $respone =[
            'status'=> 'success',
            'data'=>$user,
        ];
        }else {
            $respone =[
                'status'=> 'error',
                'data'=>$user,
            ];
        }
    }
    public function update(Request $request, User $user)
    {
        echo $request->method();
        return $request->all();
    }
    public function delete(User $user)
    {
        return $user;
    }
}
