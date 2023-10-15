<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;

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

        

        $user = $user->with('posts') ->paginate();

        if ($user->count()) {
            $statusCode = '200';
            $statusText = 'success';
        } else {
            $statusCode = '204';
            $statusText = 'no_data';
        }

      

        $users = new UserCollection($user,$statusCode,$statusText);

        // $users=UserResource::collection($user);
        // $response = [
        //     'status' => $status,
        //     'data' => $users,
        // ];
        return $users;
    }
    public function detail($id)
    {
        $user = User::with('posts')->find($id);
        if (!$user) {
            $statusCode = '404';
            $statusText = 'no_data';
        } else {
            $statusCode = '200';
            $statusText = 'success';
            $user = new UserResource($user);
        }

       

        $response = [
            'status' => $statusCode,
            'title' => $statusText,
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
                'status' => 201,
                'title'=>'create',
                'data' => $user,
            ];
        } else {
            $response = [
                'status' => 500,
                'title'=> 'server error',
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
        $status =User::destroy($user->id);
        
        if ($status) {
            $response =[
                'status'=>'success',
            ];
        }else {
            $response =[
                'status'=>'error',
            ];
        }

        return $response;
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
