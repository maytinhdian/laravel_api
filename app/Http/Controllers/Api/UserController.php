<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        return 'All Users';
    }
    public function detail($id){
        return 'User '.$id;
    }
    public function create(Request $request){
        return $request->all();
    }
    public function update(Request $request,$id){
        echo $request->method();
        return $request->all();
    }
    public function delete($id){
        return $id;
    }
    
}
