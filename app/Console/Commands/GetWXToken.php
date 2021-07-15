<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetWXToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:wx {appid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Wx token by Tencent Cloud Authorizated';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function generateCurl($appid) {
        if(empty($appid)) {
            return false;
        }

        $secretId = env('TC_SecretId');
        $secretKey = env('TC_SecretKey');
        $host = "lowcode.tencentcloudapi.com";
        $service = "lowcode";
        $version = "2021-01-08";
        $action = "DescribeWxAccessToken";
        $region = "ap-beijing";
        $timestamp = time();
        // $timestamp = 1551113065;
        $algorithm = "TC3-HMAC-SHA256";

        // step 1: build canonical request string
        $httpRequestMethod = "POST";
        $canonicalUri = "/";
        $canonicalQueryString = "";
        $canonicalHeaders = "content-type:application/json; charset=utf-8\n"."host:".$host."\n";
        $signedHeaders = "content-type;host";
        $payload = '{"WxAppId":"'.$appid.'","ComponentAppId":"'.env('WX_ComponentAppId').'"}';
        $hashedRequestPayload = hash("SHA256", $payload);
        $canonicalRequest = $httpRequestMethod."\n"
            .$canonicalUri."\n"
            .$canonicalQueryString."\n"
            .$canonicalHeaders."\n"
            .$signedHeaders."\n"
            .$hashedRequestPayload;
        // echo $canonicalRequest.PHP_EOL;

        // step 2: build string to sign
        $date = gmdate("Y-m-d", $timestamp);
        $credentialScope = $date."/".$service."/tc3_request";
        $hashedCanonicalRequest = hash("SHA256", $canonicalRequest);
        $stringToSign = $algorithm."\n"
            .$timestamp."\n"
            .$credentialScope."\n"
            .$hashedCanonicalRequest;
        // echo $stringToSign.PHP_EOL;

        // step 3: sign string
        $secretDate = hash_hmac("SHA256", $date, "TC3".$secretKey, true);
        $secretService = hash_hmac("SHA256", $service, $secretDate, true);
        $secretSigning = hash_hmac("SHA256", "tc3_request", $secretService, true);
        $signature = hash_hmac("SHA256", $stringToSign, $secretSigning);
        // echo $signature.PHP_EOL;

        // step 4: build authorization
        $authorization = $algorithm
            ." Credential=".$secretId."/".$credentialScope
            .", SignedHeaders=content-type;host, Signature=".$signature;
        // echo $authorization.PHP_EOL;

        $curl = "curl -s -X POST https://".$host
            .' -H "Authorization: '.$authorization.'"'
            .' -H "Content-Type: application/json; charset=utf-8"'
            .' -H "Host: '.$host.'"'
            .' -H "X-TC-Action: '.$action.'"'
            .' -H "X-TC-Timestamp: '.$timestamp.'"'
            .' -H "X-TC-Version: '.$version.'"'
            .' -H "X-TC-Region: '.$region.'"'
            ." -d '".$payload."'";
        // echo $curl.PHP_EOL;
        return $curl;
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //获取参数
        $args = $this->arguments();

        //处理组合参数
        $appid = $args['appid'];

        $curl = $this->generateCurl($appid);
        
        $resp = exec($curl);

        echo $resp;

        return 0;
    }
}
