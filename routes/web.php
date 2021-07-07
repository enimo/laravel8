<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EssayController;
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
    return view('user.index', ['name' => 'Rocky']);
});

Route::get('user',[UserController::class, 'index']);
// Route::get('user', 'UserController@index');
Route::get('user/{id}', [UserController::class, 'get_user']);


Route::get('/index', function () {
    return view('index', 'EssayController@index_tpl');
});
Route::get('show/{id}', 'EssayController@detail_tpl');


// 设备注册等相关
Route::group(array('prefix' => 'v1/user', 'middleware' => 'VerifyApikey'), function() {

	Route::get('listdevice', 'DevicesController@getList'); 

	// 对于Post的请求Laravel默认进行csrf校验，故需要在 Middleware/VerifyCsrfToken.php中加入post路由的白名单
	Route::post('addjpid', 'DevicesController@addJpushRegid'); 

});