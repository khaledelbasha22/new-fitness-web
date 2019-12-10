<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group( array('prefix' => 'categories'), function() {
    Route::get('all', 'Api\CategoriesController@AllCategories');
    Route::post('Videos', 'Api\CategoriesController@CategoryVideos');
});

Route::group( array('prefix' => 'nutrition'), function() {
    Route::get('all', 'Api\NutritionController@AllNutrition');
});



Route::group( array('prefix' => 'user'), function() {
        Route::post('login', 'Api\UsersController@Login');
        Route::post('register', 'Api\UsersController@Register');
        Route::post('info', 'Api\UsersController@GetUserData');

});


Route::group( array('prefix' => 'training'), function() {
    Route::get('schedule', 'Api\TrainingScheduleController@TrainingSchedule');
    Route::post('schedule', 'Api\TrainingScheduleController@TrainingSchedule');
    Route::post('complete', 'Api\TrainingScheduleController@CompleteExercise');
});

Route::group( array('prefix' => 'videos'), function() {
    Route::get('free', 'Api\VideosController@GetFreeVideos');
    Route::get('categories', 'Api\VideosController@GetFreeVideos');
});

Route::group( array('prefix' => 'challenges'), function() {
    Route::get('all', 'Api\ChallengesController@AllChallenges');
    Route::post('challenge', 'Api\ChallengesController@ChallengeData');
});



Route::get('/UpdateData', 'Api\VideosController@UpdateData');
