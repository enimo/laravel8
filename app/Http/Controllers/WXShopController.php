<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Console\Commands\GetWXToken;

class WXShopController extends Controller
{
    //

    public static $client;

    public function __construct(){

        WXShopController::$client = new Client([
            // Base URI is used with relative requests
            // 'base_uri' => 'http://httpbin.org',
            // You can set any number of default request options.
            'timeout'  => 3,
        ]);

    }

    public function getToken(Request $req) {
        // $req = $req->except('_token');
        $appid = $req->input('appid', env('MP_APPID'));
        $secret = $req->input('secret', '');
        $type = $req->input('type', 1);  // $type == 1, 默认为直连拿商户token，$type == 2，则获取第三方服务商的token
        // $password = rand(100000,1000000);

        if($type == 1) { // 
            
            $res = WXShopController::$client->request('GET', 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret, [
                // 'auth' => ['user', 'pass']
            ]);
            // echo $res->getStatusCode();
            // "200"
            // echo $res->getHeader('content-type')[0];
            $data = json_decode($res->getBody(), true);
        }
        else {
            $token = new GetWXToken();
            $curl = $token->generateCurl($appid);
            $data = json_decode(exec($curl), true);
            if(isset($data['Response']) && isset($data['Response']['AccessToken'])){
                $data['access_token'] = $data['Response']['AccessToken'];
            }
        }

        if($data) {
            return response()->json(array('errorCode'=>22000,'data'=>$data, 'msg'=>'ok'));
        }
        else {
            return response()->json(array('errorCode'=>22001,'data'=>$data, 'msg'=>'failed'));
        }
    }


    public function shopInfo(Request $req) {
        $access_token = $req->input('access_token', '');
        if(!empty($access_token)){
            $res = WXShopController::$client->request('POST', 'https://api.weixin.qq.com/product/store/get_info?access_token='.$access_token, [
                'body' => ' '
            ]);
            $data = json_decode($res->getBody(), true);
            return response()->json(array('errorCode'=>22000,'data'=>$data, 'msg'=>'ok'));
        }

        return response()->json(array('errorCode'=>22001,'data'=>[], 'msg'=>'access token failed'));
    }


    public function getGoods(Request $req) {
        $access_token = $req->input('access_token', '');
        if(!empty($access_token)){
            $res = WXShopController::$client->request('POST', 'https://api.weixin.qq.com/product/spu/get_list?access_token='.$access_token, [
                'body' => '{
                    "status": 5,
                    "page": 1,
                    "page_size": 10,
                    "need_edit_spu": 0      // 默认0:获取线上数据, 1:获取草稿数据
                }'
            ]);
            $data = json_decode($res->getBody(), true);
            return response()->json(array('errorCode'=>22000,'data'=>$data, 'msg'=>'ok'));
        }

        return response()->json(array('errorCode'=>22001,'data'=>[], 'msg'=>'access token failed'));
    }


    public function getBalance(Request $req) {
        $access_token = $req->input('access_token', '');
        if(!empty($access_token)){
            $res = WXShopController::$client->request('POST', 'https://api.weixin.qq.com/product/funds/getbalance?access_token='.$access_token, [
                'body' => ' '
            ]);
            $data = json_decode($res->getBody(), true);
            return response()->json(array('errorCode'=>22000,'data'=>$data, 'msg'=>'ok'));
        }

        return response()->json(array('errorCode'=>22001,'data'=>[], 'msg'=>'access token failed'));
    }


    public function getCashFlow(Request $req) {
        $access_token = $req->input('access_token', '');
        if(!empty($access_token)){
            $res = WXShopController::$client->request('POST', 'https://api.weixin.qq.com/product/funds/scanorderflow?access_token='.$access_token, [
                'body' => ' {
                    "page_num": 1,  
                    "page_size": 100 
                }'
            ]);
            $data = json_decode($res->getBody(), true);
            return response()->json(array('errorCode'=>22000,'data'=>$data, 'msg'=>'ok'));
        }

        return response()->json(array('errorCode'=>22001,'data'=>[], 'msg'=>'access token failed'));
    }

}
