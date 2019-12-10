<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Users;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function CheckLang($request){
        $local = "en";
        if ($request->input('local')){
            $local = $request->input('local');
        }
        return $local;
    }


    public function CheckLogin($request){
        $UserID = $request->input("user_id");
        if (!$UserID) {
            return false;
        }
        return Users::where('id', '=', $UserID)->first();
    }
}
