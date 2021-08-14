<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GlobalAPIController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::any("v1/{class}/{id?}/{subRequest?}/{subId?}/{otherInfo?}", [GlobalAPIController::class, 'processRequest'])->where('otherInfo', '.+');

// Route::any("api/v1/{class}/{path?}", [GlobalAPIController::class, 'parseRequest'])->where('path', '.+');


