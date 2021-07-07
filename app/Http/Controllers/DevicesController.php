<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Devices;

use Log;

class DevicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getList(Request $request) {

        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 20);
        
        $model = new Devices();
        $tplData = $model->deviceList($offset, $limit);

        if(!$tplData) {
            return response()->json(array('errorCode'=>22001,'data'=>'no data','success' => true));
        } 
        // return Response::json(Essay::get());
        return response()->json(array('errorCode'=>22000,'data'=>$tplData,'success' => true));
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * 用户设备注册.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        $scode = $request->input('secret', 'default');
        $did = $request->input('did', '');
        $extra = $request->input('extra', '');

        $client_secret = 'RMUYjgb1';
        $server_salt = 'enimo';

        if ($scode != $client_secret) { 
            return response()->json(array('errorCode'=>22002,'msg'=>'app secret error'));
        }
        else if (empty($did)  || empty($extra)) {
            return response()->json(array('errorCode'=>22003,'msg'=>'app did or extra info error'));
        }
        else {
            $apikey = md5($did.$client_secret.$server_salt.time()); //根据注册设备id+客户端秘钥+服务端秘钥+当前时间戳，生成apikey, 并存表

            $devicesModel = new Devices();
            $devicesModel->register(array(
                'did' => $did,
                'jp_regid' => $request->input('jp_regid', ''),
                'apikey' => $apikey, 
                'extra' => $extra, //设备的具体信息
                'status' => 1, // 注册设备状态，注册后默认该did启用
                'ip' => $request->getClientIp(), 
                'createtime' => time(),
                'updatetime' => time()
            ));
            $tplData = array('apikey' => $apikey);

            return response()->json(array('errorCode'=>22000,'data'=>$tplData,'msg'=>'ok'));

        }

    }

    /**
     * 存储用户设备的jpush regid，用于定点推送
     *
     * @param  Request  $request
     * @return Response
     */
    public function addJpushRegid(Request $request)
    {
        $did = $request->input('did', '');
        $regid = $request->input('regid', '');

        Log::info('add jpush_regid Controller, did='.$did.', regid='.$regid);

        if ( empty($did) || empty($regid) ) {
            return response()->json(array('errorCode'=>22003,'msg'=>'app did or regid empty'));
        }
        else {
            $model = new Devices();
            $ret = $model->addJpushRegid($did, $regid);
            if ($ret) {
                return response()->json(array('errorCode'=>22000,'msg'=>'jp regid save success'));
            }
            else {
                return response()->json(array('errorCode'=>22005,'msg'=>'jp regid save failed'));
            }
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
