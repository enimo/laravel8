<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EssayController;
use App\Http\Controllers\WXPayController;


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

// Route::get('/welcome', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return view('welcome', ['name' => 'Rocky']);
});

Route::get('user',[UserController::class, 'getList']);
// Route::get('user', 'UserController@index');
Route::get('user/{id}', [UserController::class, 'getUser']);
Route::post('useradd', [UserController::class, 'addUser']);


Route::get('order/make', [WXPayController::class, 'newOrder']);
Route::get('order/check', [WXPayController::class, 'checkOrder']);
Route::get('order/bill', [WXPayController::class, 'checkBill']);
Route::get('order/downloadbill', [WXPayController::class, 'downloadBill']);
Route::get('order/pay', [WXPayController::class, 'payCash']);


Route::get('index',[EssayController::class, 'index_tpl']);
Route::get('about',[EssayController::class, 'about_tpl']);
Route::get('show/{id}', [EssayController::class, 'detail_tpl']);


// 设备注册等相关
Route::group(array('prefix' => 'v1/user', 'middleware' => 'VerifyApikey'), function() {

	Route::get('listdevice', 'DevicesController@getList'); 

	// 对于Post的请求Laravel默认进行csrf校验，故需要在 Middleware/VerifyCsrfToken.php中加入post路由的白名单
	Route::post('addjpid', 'DevicesController@addJpushRegid'); 

});