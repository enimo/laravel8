<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EssayController;
use App\Http\Controllers\WXPayController;
use App\Http\Controllers\WXShopController;


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



// PAGE
// Route::get('/', function () {
//     return view('welcome', ['name' => 'Rocky']);
// });

Route::get('user',[UserController::class, 'userlist_tpl']);
// Route::get('user', 'UserController@index');
Route::get('user/{id}', [UserController::class, 'user_tpl']);

Route::get('/',[EssayController::class, 'weda_index_tpl']);  
Route::get('sns',[EssayController::class, 'sns_tpl']);

Route::get('essay', [EssayController::class, 'essayList_tpl']);
Route::get('essay/{id}', [EssayController::class, 'detail_tpl']);

Route::get('about',[EssayController::class, 'about_tpl']); // 暂时未展示该页面的入口


Route::get('wxpay',[WXPayController::class, 'wxpay_tpl']); // 随机生成微信支付二维码




// API
Route::post('useradd', [UserController::class, 'addUser']);

Route::get('order/make', [WXPayController::class, 'newOrder']);
Route::get('order/check', [WXPayController::class, 'checkOrder']);
Route::get('order/bill', [WXPayController::class, 'checkBill']);
Route::get('order/downloadbill', [WXPayController::class, 'downloadBill']);
Route::get('order/pay', [WXPayController::class, 'payCash']);


Route::get('shop/token',[WXShopController::class, 'getToken']);
Route::get('shop/info',[WXShopController::class, 'shopInfo']);
Route::get('shop/goods', [WXShopController::class, 'getGoods']);
Route::get('shop/balance', [WXShopController::class, 'getBalance']);
Route::get('shop/cashflow', [WXShopController::class, 'getCashFlow']);



// 设备注册等相关
Route::group(array('prefix' => 'v1/user', 'middleware' => 'VerifyApikey'), function() {

	Route::get('listdevice', 'DevicesController@getList'); 

	// 对于Post的请求Laravel默认进行csrf校验，故需要在 Middleware/VerifyCsrfToken.php中加入post路由的白名单
	Route::post('addjpid', 'DevicesController@addJpushRegid'); 

});