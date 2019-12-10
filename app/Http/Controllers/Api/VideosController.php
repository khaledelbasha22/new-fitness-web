<?php

namespace App\Http\Controllers\Api;

use App\NutritionLang;
use App\Nutritions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Videos;

class VideosController extends Controller
{
    public function GetFreeVideos(Request $request){
        $Local = $this->CheckLang($request);
        $FreeVideos = Videos::leftJoin('videos_lang', 'videos.id', '=', 'videos_lang.video_id')
            ->where('is_free', '1')
            ->where('videos_lang.lang_code', $Local)->limit('5')
            ->get();
        if (empty($FreeVideos->count())) {
            return response()->json([
                'error' => true,
                'message' => "can't found videos"
            ]);
        }else{
            return response()->json([
                'error' => false,
                'message' => "can't found videos",
                'videos'=> $FreeVideos
            ]);
        }
    }



    public function UpdateData(){
//        $Categories = Categories::all();
//        foreach ($Categories as $Category){
//            $ARCategory = new CategoriesLang();
//            $ARCategory->category_id = $Category->id;
//            $ARCategory->lang = "Arabic";
//            $ARCategory->lang_code = "ar";
//            $ARCategory->title = $Category->title_ar;
//            $ARCategory->save();
//
//
//            $ENCategory = new CategoriesLang();
//            $ENCategory->category_id = $Category->id;
//            $ENCategory->lang = "English";
//            $ENCategory->lang_code = "en";
//            $ENCategory->title = $Category->title_en;
//            $ENCategory->save();
//        }


        $Videos = Videos::where('is_nutrition', '=', '1')->get();
        foreach ($Videos as $Video) {
            $Nutritions = new Nutritions();
            $Nutritions->save();
            foreach ($Video->Languages as $Language) {
                $NutritionLang = new NutritionLang();
                $NutritionLang->nutrition_id = $Nutritions->id;
                $NutritionLang->lang = $Language->lang;
                $NutritionLang->lang_code = $Language->lang_code;
                $NutritionLang->title = $Language->title;
                $NutritionLang->description = $Language->description;
                $NutritionLang->save();

            }
        }

    }
}
