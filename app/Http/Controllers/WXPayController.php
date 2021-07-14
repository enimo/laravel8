<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use WeChatPay\Builder;
use WeChatPay\Util\PemUtil;
use WeChatPay\Transformer;


use GuzzleHttp\Exception\RequestException;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;
// use WechatPay\GuzzleMiddleware\Util\PemUtil;

class WXPayController extends Controller
{
    //

    public static $instance1;
    public static $instance2;

    public function __construct(){

        // 采用新版的链式工厂方法构造一个实例1
        WXPayController::$instance1 = Builder::factory([
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
            'secret' => env('PAY_API_SECRET'),
            'merchant' => [// --不使用APIv2可选
                // 商户证书 文件路径 --不使用APIv2可选
                'cert' => env('mchcert'),
                // 商户API私钥 文件路径 --不使用APIv2可选
                'key' => env('privateKey'),
            ],
        ]);

        
        // 采用原始的Guzzle HTTP 工厂方法构造一个实例2
        $merchantId = env('MCHID'); // 商户号
        $merchantSerialNumber = env('mchSERIAL'); // 商户API证书序列号
        $merchantPrivateKey = PemUtil::loadPrivateKey(env('privateKey')); // 商户私钥
        // 微信支付平台配置
        $wechatpayCertificate = PemUtil::loadCertificate(env('platcert')); // 微信支付平台证书

        // 构造一个WechatPayMiddleware
        $wechatpayMiddleware = WechatPayMiddleware::builder()
            ->withMerchant($merchantId, $merchantSerialNumber, $merchantPrivateKey) // 传入商户相关配置
            ->withWechatPay([ $wechatpayCertificate ]) // 可传入多个微信支付平台证书，参数类型为array
            ->build();

        // 将WechatPayMiddleware添加到Guzzle的HandlerStack中
        $stack = \GuzzleHttp\HandlerStack::create();
        $stack->push($wechatpayMiddleware, 'wechatpay');

        // 创建Guzzle HTTP Client时，将HandlerStack传入
        WXPayController::$instance2 = new \GuzzleHttp\Client(['handler' => $stack]);

    }


    public function newOrder(Request $req) {
        $name = $req->input('name', '');
        $email = $req->input('email', '');
        $password = rand(100000,1000000);

        $arr = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ];

        try {
            /*
            // 方法一  -  原始方法
            $resp = WXPayController::$instance2->request('POST', 'https://api.mch.weixin.qq.com/v3/pay/transactions/native', [
                'json' => [
                    'mchid' => env('MCHID'),
                    'out_trade_no' => '666555001',
                    'appid' => env('MP_APPID'),
                    'description' => 'Image形象店-深圳腾大-QQ公仔',
                    'notify_url' => 'https://weixin.qq.com/',
                    'amount' => [
                        'total' => 1,
                        'currency' => 'CNY'
                    ],
                ],
                'headers' => [ 'Accept' => 'application/json' ]
            ]);
            */

            // 方法二
            $resp = WXPayController::$instance1->v3->pay->transactions->native
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
            // echo $e->getMessage(), PHP_EOL;
            if ($e instanceof \Psr\Http\Message\ResponseInterface && $e->hasResponse()) {
                echo $e->getResponse()->getStatusCode() . ' ' . $e->getResponse()->getReasonPhrase(), PHP_EOL;
                echo $e->getResponse()->getBody();
            }

            return response()->json(array('errorCode' => 22001, 'data' => $e->getResponse()->getBody(), 'msg' => $e->getMessage() ));
        }

    }

    public function checkOrder(Request $req) {

        $tid = $req->input('tid', '4200001168202107043233484268');
        // $email = $req->input('email', '');

        $res = WXPayController::$instance1->v3->pay->transactions->id->{'{transaction_id}'}
            ->getAsync([
                // 查询参数结构
                'query' => ['mchid' => env('MCHID')],
                // uri_template 字面量参数
                'transaction_id' => $tid,
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

    // 目前查询账单相关 必须采用guzzle的实例化$instant2
    public function checkBill(Request $req) {

        $date = $req->input('date', '2021-07-04');
        $type = $req->input('type', 'ALL');
        $tradeBill = $req->input('trade', '1');
        if(intval($tradeBill) == 1) {
            $url = 'https://api.mch.weixin.qq.com/v3/bill/tradebill';
        }
        else {
            $url = 'https://api.mch.weixin.qq.com/v3/bill/fundflowbill';
        }

        try {
            $resp = WXPayController::$instance2->request('GET', $url.'?bill_date='.$date.'&bill_type='.$type, [ // 注意替换为实际URL
                'headers' => [ 'Accept' => 'application/json' ]
            ]);
            // echo $resp->getStatusCode().' '.$resp->getReasonPhrase()."\n";
            // echo $resp->getBody()."\n";
            return response()->json(array('errorCode'=>22000, 'data' => json_decode($resp->getBody()), 'msg'=> $resp->getStatusCode() . ' ' . $resp->getReasonPhrase() ));

        } catch (RequestException $e) {
            // 进行错误处理
            echo $e->getMessage()."\n";
            if ($e->hasResponse()) {
                echo $e->getResponse()->getStatusCode().' '.$e->getResponse()->getReasonPhrase()."\n";
                echo $e->getResponse()->getBody();
            }
            return;
        }

    }

    /*
    // 目前查询账单使用新版 初始化实例 有问题
    public function checkTradeBill2(Request $req) {
        $date = $req->input('date', '2021-07-04');
        $type = $req->input('type', 'ALL');

        $res = WXPayController::$instance1->chain('v3/bill/tradebill')
        // $res = WXPayController::$instance1->v3->bill->tradebill
            ->postAsync([
                'bill_date' => $date,
                'bill_type' => $type
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
                    // dd($body->getContents());
                }
                // echo $e->getTraceAsString(), PHP_EOL;
                dd($e->getTraceAsString());
            })
            ->wait();
    }
    */


    // 目前查询账单相关 必须采用guzzle的实例化$instant2
    public function downloadBill(Request $req) {

        // $date = $req->input('date', '2021-07-04');
        $token = $req->input('token', '');

        try {
            $resp = WXPayController::$instance2->request('GET', 'https://api.mch.weixin.qq.com/v3/billdownload/file?token='.$token, [ // 注意替换为实际URL
                'headers' => [ 'Accept' => 'application/json' ]
            ]);

            // echo $resp->getStatusCode().' '.$resp->getReasonPhrase()."\n";
            echo $resp->getBody()."\n";

        } catch (RequestException $e) {
            // 进行错误处理
            echo $e->getMessage()."\n";
            if ($e->hasResponse()) {
                echo $e->getResponse()->getStatusCode().' '.$e->getResponse()->getReasonPhrase()."\n";
                echo $e->getResponse()->getBody();
            }
            return;
        }

    }


    public function payCash(Request $req) {

        $openid = $req->input('openid', env('OPENID'));
        // $email = $req->input('email', '');

        $res = WXPayController::$instance1->v2->mmpaymkttransfers->promotion->transfers
        ->postAsync([
            'xml' => [
              'appid' => env('MP_APPID'),
              'mch_id' => env('MCHID'),
              'partner_trade_no' => '666555002',
              'openid' => $openid,
              'check_name' => 'NO_CHECK', //'FORCE_CHECK',
              're_user_name' => '', //王石
              'amount' => 10,
              'desc' => '理赔',
              'spbill_create_ip' => $_SERVER['REMOTE_ADDR'], //'192.168.0.1',
            ],
            'security' => true,
            'debug' => true //开启调试模式
        ])
        ->then(static function($response) { 
            return Transformer::toArray($response->getBody()->getContents()); 
        })
        ->otherwise(static function($exception) { 
            dd($exception);
            return Transformer::toArray($exception->getResponse()->getBody()->getContents()); 
        })
        ->wait();

        print_r($res);


    }



}
