<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;


use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Essay;
use App\Models\Likes;
use App\Models\Votes;
use App\Models\Cards;


class EssayController extends Controller
{

    /**
     * 首页模板
     *
     * @return Response
     */
    public function index_tpl()
    {
        if (strpos($_SERVER["SERVER_NAME"], 'vip.enimo.cn') !== FALSE) {
            return response()->json(array('errorCode'=>22004, 'msg'=>'no index page'));
        }
        $tplData = array();
        return view("index", array('tplData' => $tplData));
    }

    public function about_tpl()
    {
        $essayModel = new Essay();
        $tplData = $essayModel->getDetail(78);
        if (!$tplData) {
            abort(404);
            return false;
        }
        return view("detail", array('tplData' => $tplData));
    
    }

    public function getapp()
    {
        // 默认为PC端
        $isWap = false;
        //获取USER AGENT
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        //分析数据
        $is_pc = (strpos($userAgent, 'windows nt')) ? true : false;   
        $is_mac = (strpos($userAgent, 'macintosh')) ? true : false;   
        $is_iphone = (strpos($userAgent, 'iphone')) ? true : false;   
        $is_ipad = (strpos($userAgent, 'ipad')) ? true : false;   
        $is_android = (strpos($userAgent, 'android')) ? true : false; 
        $is_ios = false;
        if ($is_iphone || $is_ipad) {
            $is_ios = true;
        }
        if ($is_android || $is_ios) {
            $isWap = true;
        }

        // if (isset($_GET['getapp'])) {
            if ($is_ios) {
                header('Location: https://itunes.apple.com/app/apple-store/id1011062013?pt=117720940&ct=www&mt=8');
                // header('Location: https://itunes.apple.com/app/apple-store/id1011062013?mt=8');
            }
            else {
                header('Location: http://xxxx.apk');
            }
        // }
        // else {
        //     header("HTTP/1.1 404 Not Found");  
        //     header("Status: 404 Not Found");  
        //     exit; 
        // }

    }

    /**
     * 详情页模板
     *
     * @return Response
     */
    public function detail_tpl(Request $request, $id)
    {
        
        if((int)$id == 0 || empty($id)) {
            abort(404);
        }
        
        $essayModel = new Essay();
        $tplData = $essayModel->getDetail($id);
        if (!$tplData) {
            abort(404);
            return false;
        }

        // 访问数+1
        $bnfObj =  Essay::where('essay_id', $id);
        $bnfObj->increment('essay_click', 1);
        // $tplData = $this->formatData($tplData);
        return view("detail", array('tplData' => $tplData));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function essayList(Request $request, $cid)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 8);
        $did = $request->input('did', 0);
        $cid = (int) $cid;
        
        $essayModel = new Essay();
        $tplData = $essayModel->getList($offset, $limit, $cid, $did);

        if(!$tplData) {
            return response()->json(array('errorCode'=>22000,'data'=>array(), 'msg'=>'no data'));
        } 
        // return Response::json(Essay::get());
        return response()->json(array('errorCode'=>22000, "count"  => count($tplData), 'data'=>$tplData));

    }

    /**
     * 根据用户个性化推送的内容
     *
     * @return Response
     */
    public function feedList(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 8);
        $did = $request->input('did', 0);

        // 先默认选中“热门栏目”的信息
        $cid = 23;
        
        $essayModel = new Essay();
        $tplData = $essayModel->getList($offset, $limit, $cid, $did);


        $cardModel = new Cards();
        $isDefaultData = $cardModel->hasCard($did) ? 0 : 1; // 是否保存过卡片
        $extra = array("isDefaultData" => $isDefaultData);

        if(count($tplData) > 1) {
            foreach ($tplData as $key => $value) {
                $tplData[$key]['avatar'] = 'http://ym.enimo.cn/static/img/logo.png';
            }
        }

        if(!$tplData) {
            return response()->json(array('errorCode'=>22000,'data'=>array(), 'msg'=>'no data'));
        } 

        // return Response::json(Essay::get());
        return response()->json(array('errorCode'=>22000, "count"  => count($tplData), 'extra'=>$extra, 'data'=>$tplData));

    }


    /**
     * Display a listing of the recommend.
     *
     * @return Response
     */
    public function recommend(Request $request, $eid)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 3);
        $did = $request->input('did', 0);
        $eid = (int) $eid;
        
        $essayModel = new Essay();
        $tplData = $essayModel->getRecommend($offset, $limit, $eid, $did);

        if(!$tplData) {
            return response()->json(array('errorCode'=>22000,'data'=>array(),'msg'=>'no data'));
        } 
        // return Response::json(Essay::get());
        return response()->json(array('errorCode'=>22000, "count"  => count($tplData), 'data'=>$tplData));

    }


    /**
     * Display a listing of the search.
     *
     * @return Response
     */
    public function search(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 3);
        $did = $request->input('did', 0);
        $wd = $request->input('wd', 0);
        
        $essayModel = new Essay();
        $tplData = $essayModel->getSearch($offset, $limit, $wd, $did);

        if(!$tplData) {
            return response()->json(array('errorCode'=>22000,'data'=>array(),'msg'=>'no data'));
        } 
        // return Response::json(Essay::get());
        return response()->json(array('errorCode'=>22000, "count"  => count($tplData), 'data'=>$tplData));

    }


    /**
     * Display a listing of the search tips
     *
     * @return Response
     */
    public function hotSearch(Request $request)
    {

        $did = $request->input('did', 0);
        
        $essayModel = new Essay();
        $es = $essayModel->getDetail(89, $did); // 搜索热词，从id=89的文章里面取
        $wd_list_str = trim(strip_tags($es['essay_content']));
        $wd_list_arr = explode('#', $wd_list_str);
        $tplData = array(
                'bank'=>explode("|", $wd_list_arr[0]), 
                'inn'=>explode("|", $wd_list_arr[1]), 
                'air'=>explode("|", $wd_list_arr[2]), 
                'other'=>explode("|", $wd_list_arr[3]), 
                
        );

        return response()->json(array('errorCode'=>22000, "count"  => count($tplData), 'data'=>$tplData));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $init_clicks = rand(100,500);
        $source = str_replace('https://', '', str_replace('http://', '', $request->input('source', '网络')));
        Essay::create(array(
            'essay_title' => $request->input('title'),
            'essay_content' => $request->input('content'),
            'essay_author' => $request->input('author'), // 大玩家/值得买
            'essay_editor' => 'ym',
            'essay_source' => $source, // 来源网站、url
            // 'essay_source' => $request->input('source', '网络'), // 来源网站、url
            'essay_tag' => $request->input('tags', '酒店|机票'), // 标签
            'essay_categoryid' => 134,
            'essay_click'=> $init_clicks,
            'essay_vote'=>(int) ($init_clicks/5),

            'essay_createtime' => time(),
            'essay_updatetime' => date("Y-m-d H:i:s")
        ));

        return response()->json(array('errorCode'=>22000,'data'=>'ok'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Request $request, $id)
    {
        $did = $request->input('did', 0);
        if((int)$id == 0 || empty($id)) {
            abort(404);
        }
        
        $essayModel = new Essay();
        $tplData = $essayModel->getDetail($id, $did);

        if(!$tplData) {
            return response()->json(array('errorCode'=>22001,'data'=>array(),'msg'=>'no data'));
        } 
        // 访问数+1
        $bnfObj =  Essay::where('essay_id', $id);
        $bnfObj->increment('essay_click', 1);
        return response()->json(array('errorCode'=>22000,'data'=>$tplData));
        // return response()->json(Essay::where('essay_id', $id)->get());
    }


    /**
     * Like List API.
     *
     * @return Response
     */
    public function likeList(Request $request)
    {
        $did = $request->input('did');
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        // exit($did);
        // $status = ($action == 'add') ? 1 : 0; //del
        if (!isset($did)) {
            return response()->json(
                array(
                    "errorCode" => 22002,
                    "data"  => 'params error'
                )
            );
        }

        $likeObj = new Likes();
        $tplData = $likeObj->likeList($offset, $limit, $did);

        return response()->json(
            array(
                "errorCode" => 22000,
                "count"  => count($tplData),
                "data"  => $tplData
            )
        );
    }


    /**
     * Like action API.
     *
     * @return Response
     */
    public function like($action, Request $request)
    {
        $did = $request->input('did', 0);
        $eid = $request->input('eid', 0);
        $status = ($action == 'add') ? 1 : 0; //del
        if ($eid == 0) {
            return response()->json(
                array(
                    "errorCode" => 22002,
                    "data"  => 'params error'
                )
            );
        }

        $likeObj = new Likes();
        $tplData = $likeObj->actionHandler($did, $eid, $status);

        return response()->json(
            array(
                "errorCode" => 22000,
                "data"  => $tplData
            )
        );
    }


    /**
     * Vote action API.
     *
     * @return Response
     */
    public function vote($action, Request $request)
    {
        $did = $request->input('did', 0);
        $eid = $request->input('eid', 0);
        $status = ($action == 'add') ? 1 : 0; //del
        if ($eid == 0) {
            return response()->json(
                array(
                    "errorCode" => 22002,
                    "data"  => 'params error'
                )
            );
        }

        $voteObj = new Votes();
        $tplData = $voteObj->actionHandler($did, $eid, $status);

        return response()->json(
            array(
                "errorCode" => 22000,
                "data"  => $tplData
            )
        );
    }

    /**
     * Hot Category action API.
     *
     * @return Response
     */
    public function hotCateList(Request $request)
    {
        $topPid = 123;
        $middlePid = 129;

        $essayObj = new Essay();
        $tplData = array("top"=>$essayObj->getCateList($topPid), "mid"=>$essayObj->getCateList($middlePid));

        if(!$tplData) {
            return response()->json(array('errorCode'=>22001,'data'=>array(),'msg'=>'no data'));
        }

        return response()->json(
            array(
                "errorCode" => 22000,
                "count"  => count($tplData),
                "data"  => $tplData
            )
        );
    }

    /**
     * Category action API.
     *
     * @return Response
     */
    public function cateList($pid, Request $request)
    {
        $pid = (int) $pid;
        if ($pid <= 0) {
            return response()->json(
                array(
                    "errorCode" => 22002,
                    "data"  => 'params error'
                )
            );
        }

        $essayObj = new Essay();
        $tplData = $essayObj->getCateList($pid);

        if(!$tplData) {
            return response()->json(array('errorCode'=>22001,'data'=>array(),'msg'=>'no data'));
        }

        return response()->json(
            array(
                "errorCode" => 22000,
                "count"  => count($tplData),
                "data"  => $tplData
            )
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    // public function edit($id)
    // {
    //     //
    // }

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
    // public function destroy($id)
    // {
    //     Essay::destroy($id);

    //     return response()->json(array('success' => true));
    // }
}
