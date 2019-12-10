<?php

namespace App\Http\Controllers\Api;

use App\UserInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Users;

class UsersController extends Controller
{

    public function Login (Request $request){
        $email = $request->input("email");
        $password = $request->input("password");
        $UserData = Users::where('email','=',$email)->first();
        if($UserData){
            if (Hash::check($password, $UserData->password)) {
                $ReturnData = [
                    'error' => false,
                    'message' => "",
                    'user_data' => $UserData
                ];
                return response()->json($ReturnData);
            }
        }
        $ReturnData = [
            'error' => true,
            'message' => "",
        ];

        return response()->json($ReturnData);
    }



    public function Register(Request $request){
        $fullname = $request->input("full_name");
        $email = $request->input("email");
        $password = $request->input("password");
        $gender = $request->input("gender");
        $weight = $request->input("weight");
        $height = $request->input("height");

        $password = Hash::make($password);


        $User = new Users();
        $User->role = "user";
        $User->full_name = $fullname;
        $User->email = $email;
        $User->password = $password;
        $User->gender = $gender;
        $User->archive = 1;
        $User->is_pending = 0;
        if($User->save()){
            $UserInfo = new UserInfo();
            $UserInfo->user_id = $User->id;
            $UserInfo->weight = $weight;
            $UserInfo->height = $height;
            $UserInfo->save();
            $ReturnData = [
                'error' => false,
                'message' => "",
                'user_data' => $User
            ];

        }else{
            $ReturnData = [
                'error' => true,
                'message' => ""
            ];
        }
        return response()->json($ReturnData);


    }

    public function GetUserData(Request $request){
        $UserID = $request->input("user_id");
        $UserData = Users::select(["users.id","users.role","users.full_name","users.email","users.gender","users.archive","users.is_pending","user_info.weight","user_info.height"])
            ->leftjoin('user_info', 'users.id', '=', 'user_info.user_id')
            ->where('users.id','=',$UserID)->first();
        $ReturnData = [
            'error' => true,
            'message' => ""
        ];
        if($UserData){
            $ReturnData = [
                'error' => false,
                'message' => "",
                'user_data' => $UserData
            ];
        }
        return response()->json($ReturnData);
    }

}
