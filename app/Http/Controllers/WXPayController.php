<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use WeChatPay\Builder;
use WeChatPay\Util\PemUtil;


class WXPayController extends Controller
{
    //

    public static $instance;

    public function __construct(){
        // 工厂方法构造一个实例
        WXPayController::$instance = Builder::factory([
            // 商户号
            'mchid' => env('MCHID'),
            // 商户证书序列号
            'serial' => env('SERIAL'),
            // 商户API私钥 PEM格式的文本字符串或者文件resource
            'privateKey' => PemUtil::loadPrivateKey(env('privateKey')),
            'certs' => [
                // 可由内置的平台证书下载器 `./bin/CertificateDownloader.php` 生成
                '43A8CB836394D69F361F3DFB8A323CE979CC040E' => PemUtil::loadCertificate(env('certs'))
            ],
            // APIv2密钥(32字节)--不使用APIv2可选
            'secret' => env('SECRET'),
            'merchant' => [// --不使用APIv2可选
                // 商户证书 文件路径 --不使用APIv2可选
                'cert' => env('certs'),
                // 商户API私钥 文件路径 --不使用APIv2可选
                'key' => env('privateKey'),
            ],
        ]);
    }


    public function newOrder(Request $req) {
        // $req = $req->except('_token');
        $name = $req->input('name', '');
        $email = $req->input('email', '');
        $password = rand(100000,1000000);

        $arr = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ];
        // $user = DB::table('users')->insert($arr);
        // $user = User::create($arr);

        try {
            $resp = WXPayController::$instance->v3->pay->transactions->native->post(['json' => [
                'mchid' => env('MCHID'),
                'out_trade_no' => '666555001',
                'appid' => env('MP_APPID'),
                'description' => 'Image形象店-深圳腾大-QQ公仔',
                'notify_url' => 'https://weixin.qq.com/',
                'amount' => [
                    'total' => 1,
                    'currency' => 'CNY'
                ],
            ]]);
        
            echo $resp->getStatusCode() . ' ' . $resp->getReasonPhrase(), PHP_EOL;
            echo $resp->getBody(), PHP_EOL;

        } catch (Exception $e) {
            // 进行错误处理
            echo $e->getMessage(), PHP_EOL;
            if ($e instanceof \Psr\Http\Message\ResponseInterface && $e->hasResponse()) {
                echo $e->getResponse()->getStatusCode() . ' ' . $e->getResponse()->getReasonPhrase(), PHP_EOL;
                echo $e->getResponse()->getBody();
            }
        }


        if($user) {
            return response()->json(array('errorCode'=>22000,'data'=>array(), 'msg'=>'ok'));
        }
        else {
            return response()->json(array('errorCode'=>22001,'data'=>array(), 'msg'=>'failed'));
        }
    }

    public function checkOrder(Request $req) {


        $name = $req->input('name', '');
        $email = $req->input('email', '');
        $password = rand(100000,1000000);


        $res = WXPayController::$instance->v3->pay->transactions->id->{'{transaction_id}'}
        ->getAsync([
            // 查询参数结构
            'query' => ['mchid' => env('MCHID')],
            // uri_template 字面量参数
            'transaction_id' => '1217752501201407033233368018',
        ])
        ->then(static function($response) {
            // 正常逻辑回调处理
            echo $response->getBody()->getContents(), PHP_EOL;
            return $response;
        })
        ->otherwise(static function($exception) {
            // 异常错误处理
            if ($exception instanceof \Psr\Http\Message\ResponseInterface) {
                $body = $exception->getResponse()->getBody();
                echo $body->getContents(), PHP_EOL, PHP_EOL, PHP_EOL;
            }
            echo $exception->getTraceAsString(), PHP_EOL;
        })
        ->wait();


    }


}
