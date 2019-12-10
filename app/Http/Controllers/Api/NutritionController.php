<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Nutritions;

class NutritionController extends Controller
{
    public function AllNutrition(Request $request)
    {
        $Local = $this->CheckLang($request);
        $UserData = $this->CheckLogin($request);

//        if (!$UserData) {
//            return response()->json([
//                'error' => true,
//                'active' => false,
//                'message' => "User Not Active"
//            ]);
//        }

        $AllNutritions = Nutritions::select(['nutritions.*', 'nutrition_lang.lang', 'nutrition_lang.lang_code', 'nutrition_lang.title', 'nutrition_lang.description'])
            ->leftJoin('nutrition_lang', 'nutritions.id', '=', 'nutrition_lang.nutrition_id')
            ->where('nutrition_lang.lang_code', $Local)
            ->limit(8)
            ->get();

        if (!$AllNutritions->count()) {
            return response()->json([
                'error' => true,
                'active' => true,
                'message' => "can't found Nutrition's"
            ]);
        } else {
            return response()->json([
                'error' => false,
                'active' => true,
                'message' => "",
                'nutritions' => $AllNutritions
            ]);
        }


    }
}
