<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Http\Response;


use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Essay;

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

        return view('user.index', ['users' => 'Rocky Index']);
    }

    public function get_user()
    {
        $essayModel = new Essay();
        $tplData = $essayModel->getDetail(110); // 关于我们数据
        return view("user.detail", array('tplData' => $tplData));
    }
}