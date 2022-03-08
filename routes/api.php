<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/is_user_loggedin', [AuthController::class, 'is_user_loggedin']);

Route::post('/admin_login', [AdminController::class, 'login']);
Route::post('/sendmsg', [MessageController::class, 'sendmsg']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/get_all_users', [AuthController::class, 'get_all_users']);
    Route::get('/get_all_messages', [MessageController::class, 'get_all_messages']);
    Route::get('/get_new_promoters', [AuthController::class, 'get_new_promoters']);
    Route::get('/get_promoters', [AuthController::class, 'get_promoters']);
    Route::get('/get_users', [AuthController::class, 'get_users']);
    Route::get('/get_user_detail/{user_id}', [AuthController::class, 'get_user_detail']);
    Route::post('/pay', [PaymentController::class, 'pay']);
    Route::post('/pay_promoter', [PaymentController::class, 'pay_promoter']);
    Route::get('/get_user_payments/{user_id}', [PaymentController::class, 'get_user_payments']);
    Route::post('/update_user_status', [AuthController::class, 'update_user_status']);
    Route::post('/update_profile', [AuthController::class, 'update_profile']);
    Route::post('/sub', [AuthController::class, 'sub']);
    Route::post('/promote', [AuthController::class, 'promote']);
    
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    // return $request->user();
    if ($request->user()->username) {
    	return [
	    	'status' => 'success',
	        'message' => 'admin'
	    ];
    }else{
    	return [
	    	'status' => 'success',
	        'message' => 'user'
	    ];
    }
});