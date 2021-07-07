<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Http\Response;


use App\Http\Requests;
use App\Http\Controllers\Controller;

// use App\Models\Essay;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Show a list of all of the application's users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $users = DB::select('select * from users where active = ?', [1]);

        return view('user.index', ['name' => 'Rocky User List']);
    }

    public function get_user()
    {
        $essayModel = new Essay();
        $tplData = $essayModel->getDetail(110); // 关于我们数据
        return view("user.detail", array('tplData' => $tplData));
    }

    //用户登录接口
    public function login()
    {
        //获取表单提交数据,并过滤_token字段
        // $input = $request->except('_token');
        $input = [
            'username' => '1',
            'password' => '12345',
            'code' => '1',
        ];

        //进行表单验证 Validator::make(需要验证的数据, 验证规则, 错误提示);
        $rule = [
            'username' => 'required',
            'password' => 'required | min:6',
            'code' => 'required',
        ];
        $msg = [
            'username.required' => '用户名不能为空',
            'password.required' => '密码不能为空',
            'password.min' => '密码不能少于6位',
            'code.required' => '请输入验证码',
        ];
        $validator = Validator::make($input, $rule, $msg);
        $user = Users::where('username', $input['username'])->first()->toArray();
		//新增一些其他规则
        $validator->after(function ($validator) use ($input, $user) {
            if ($input['code'] != session('code')) {
                $validator->errors()->add('code', '验证码输入错误');
            }
            if ($user['password'] != $input['username']) {
                $validator->errors()->add('code', '密码错误');
            }
        });

        if ($validator->fails()) {
            // return $validator->errors(); 直接返回所有错误数据
            return redirect('user/index')->withErrors($validator, 'user')->withInput();
        } else {
            return redirect('user/index');
        }
    }

}