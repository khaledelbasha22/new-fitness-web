<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Categories;
use App\Videos;

class CategoriesController extends Controller
{
    public function AllCategories(Request $request){
        $Local = $this->CheckLang($request);

        $AllCategories = Categories::select(['categories.*','categories_lang.lang','categories_lang.lang_code','categories_lang.title'])
            ->leftJoin('categories_lang', 'categories.id', '=', 'categories_lang.category_id')
            ->where('categories_lang.lang_code', $Local)
            ->get();

        if (empty($AllCategories->count())) {
            return response()->json([
                'error' => true,
                'message' => "can't found categories"
            ]);
        }else{
            return response()->json([
                'error' => false,
                'message' => "",
                'Categories'=> $AllCategories
            ]);
        }
    }

    public function CategoryVideos(Request $request){
        $Local = $this->CheckLang($request);

        if (!$request->input('category_id')){
            return response()->json([
                'error' => true,
                'message' => "can't found videos"
            ]);
        }

        $CategoryID = $request->input('category_id');

        $FreeVideos = Videos::select(['videos.*', 'videos_lang.lang' , 'videos_lang.lang_code' , 'videos_lang.title','videos_lang.description'])
            ->leftJoin('videos_lang', 'videos.id', '=', 'videos_lang.video_id')
            ->withcount('Views')
            ->where('category_id', $CategoryID)
            ->where('videos_lang.lang_code', $Local)
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

}

