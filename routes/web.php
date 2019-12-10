<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $total = 630;
    $number = 12;
    $percentage = 1;
    if ( $total > 0 ) {
        $percentage = round($number / ($total / 100),2);
        if($percentage < 1 ){
            $percentage =  1;
        }
        return round($percentage);
    }
    return round($percentage);
});
