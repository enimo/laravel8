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
            'serial' => env('mchSERIAL'),
            // 商户API私钥 PEM格式的文本字符串或者文件resource
            'privateKey' => PemUtil::loadPrivateKey(env('privateKey')),

            'certs' => [
                // 可由内置的平台证书下载器 `./bin/CertificateDownloader.php` 生成
                // '43A8CB836394D69F361F3DFB8A323CE979CC040E' => PemUtil::loadCertificate(env('certs'))
                env('platcertSERIAL') => PemUtil::loadCertificate(env('platcert'))
            ],

            // APIv2密钥(32字节)--不使用APIv2可选
            'secret' => env('API_SECRET'),
            'merchant' => [// --不使用APIv2可选
                // 商户证书 文件路径 --不使用APIv2可选
                'cert' => env('mchcert'),
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
            $resp = WXPayController::$instance->v3->pay->transactions->native
            ->post(['json' => [
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
        
            // echo $resp->getStatusCode() . ' ' . $resp->getReasonPhrase(), PHP_EOL;
            // echo $resp->getBody(), PHP_EOL;

            return response()->json(array('errorCode'=>22000, 'data' => json_decode($resp->getBody()), 'msg'=> $resp->getStatusCode() . ' ' . $resp->getReasonPhrase() ));

        } catch (Exception $e) {
            // 进行错误处理
            echo $e->getMessage(), PHP_EOL;
            if ($e instanceof \Psr\Http\Message\ResponseInterface && $e->hasResponse()) {
                echo $e->getResponse()->getStatusCode() . ' ' . $e->getResponse()->getReasonPhrase(), PHP_EOL;
                echo $e->getResponse()->getBody();
            }

            return response()->json(array('errorCode' => 22001, 'data' => $e->getResponse()->getBody(), 'msg' => $e->getMessage() ));

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


    public function checkBill(Request $req) {


        $date = $req->input('date', '2021-07-04');
        $type = $req->input('type', 'ALL');


        try {
            // $resp = WXPayController::$instance['v3/bill/tradebill']
            $resp = WXPayController::$instance->v3->bill->tradebill
            ->get([
                'bill_date' => $date,
                'bill_type' => $type
            ]);

            dd('2222');
            // echo $resp->getStatusCode() . ' ' . $resp->getReasonPhrase(), PHP_EOL;
            // echo $resp->getBody(), PHP_EOL;
            return response()->json(array('errorCode'=>22000, 'data' => json_decode($resp->getBody()), 'msg'=> $resp->getStatusCode() . ' ' . $resp->getReasonPhrase() ));

        } catch (Exception $e) {
            // 进行错误处理

            dd('111');
            dd($e->getMessage());

            echo $e->getMessage(), PHP_EOL;
            if ($e instanceof \Psr\Http\Message\ResponseInterface && $e->hasResponse()) {
                echo $e->getResponse()->getStatusCode() . ' ' . $e->getResponse()->getReasonPhrase(), PHP_EOL;
                echo $e->getResponse()->getBody();
            }

            return response()->json(array('errorCode' => 22001, 'data' => $e->getResponse()->getBody(), 'msg' => $e->getMessage() ));

        }

        /*
        // $res = WXPayController::$instance->chain('v3/bill/tradebill')
        $res = WXPayController::$instance->v3->bill->tradebill
            ->getAsync([
                'bill_date' => $date,
                'bill_type' => $type
                // 查询参数结构
                // 'query' => ['mchid' => env('MCHID')],
                // uri_template 字面量参数
                // 'transaction_id' => '1217752501201407033233368018',
            ])
            ->then(static function($response) {
                // 正常逻辑回调处理
                // echo $response->getBody()->getContents(), PHP_EOL;
                dd($response);
                return response()->json(array('errorCode'=>22000, 'data' => json_decode($response->getBody()->getContents()), 'msg'=> $response->getStatusCode() . ' ' . $response->getReasonPhrase() ));

            })
            ->otherwise(static function($e) {
                // 异常错误处理
                if ($e instanceof \Psr\Http\Message\ResponseInterface) {
                    $body = $e->getResponse()->getBody();
                    // echo $body->getContents(), PHP_EOL, PHP_EOL, PHP_EOL;
                    dd($body->getContents());
                }
                // echo $e->getTraceAsString(), PHP_EOL;
                dd($e->getTraceAsString());
            })
            ->wait();
            */

    }


    public function downloadBill(Request $req) {


        $date = $req->input('date', '2021-07-04');
        $type = $req->input('type', 'ALL');

        $res = WXPayController::$instance->chain('v3/bill/tradebill')
            ->getAsync([
                'bill_date' => $date,
                'bill_type' => $type
                // 查询参数结构
                // 'query' => ['mchid' => env('MCHID')],
                // uri_template 字面量参数
                // 'transaction_id' => '1217752501201407033233368018',
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
