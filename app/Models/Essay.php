<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;

use App\Models\Likes;
use App\Models\Votes;

use DB;

// date_default_timezone_set('PRC');

class Essay extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'any_essay';

    protected $connection = 'weda';

    protected $primaryKey = 'essay_id';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['essay_title', 'essay_content', 'essay_author', 'essay_click', 'essay_vote', 'essay_tag','essay_categoryid','essay_createtime','essay_updatetime','essay_editor','essay_source'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['essay_status', 'essay_isurl', 'essay_commentstatus', 'essay_commentcount', 'essay_contribute', 'essay_score', 'essay_password','essay_publisher','essay_editor']; //'essay_categoryid'

    /**
     * Get Essay List
     *
     * @var array
     */
    public function getList($offset, $limit, $cid = 23, $did = 0) {

        $ids_arr = array();

        if ($cid == 23 || $cid == 128) {
            // 如果是首页的焦点图和热门栏目，则做特殊处理，通过文章内容中填入文章id(e.g.: 112|123|125)来获取文章列表
            //cid=23->热门文章=88, cid=128->焦点图=87
            if ($cid == 23) {
                $es = Essay::getDetail(88, $did);
            }
            else {
                $es = Essay::getDetail(87, $did);
            }
            $ids_str = trim(strip_tags($es['essay_content']));
            $ids_arr = array_slice(explode('|', $ids_str), $offset, $limit); //从前面往后取，故最新的id需写在文章前面
        }
        else if ($cid == 131) {
            // 如果是今日排行，则输出当天的点赞排行榜数据
            $today = strtotime(date("Y-m-d", time()));
            // $dbData = Votes::where('vote_createtime', '>', $today)
            //             ->select('vote_essayid')
            //             ->orderBy('vote_id', 'desc') // 待优化，应该以总点赞数进行倒序 
            //             // ->orderBy('count', 'desc')
            //             ->groupBy('vote_essayid')
            //             ->skip($offset)
            //             ->take($limit)
            //             ->get();
            $sql = "SELECT vote_essayid,count(1) as count from any_vote where vote_createtime>$today group by vote_essayid order by count desc limit $offset, $limit";
            // $dbData = json_decode(json_encode(DB::select($sql)), 1);//->toArray();   
            $dbData = DB::select($sql);//->toArray();   
            
            foreach ($dbData as $val) {
                $val =  get_object_vars($val);
                $ids_arr[] = $val['vote_essayid'];
            }
        }
        else if ($cid == 132) {
            // 如果是“最新活动”，则自动筛选关键词中包含活动的文章列表
            $wd = '活动';
            $sql = 'SELECT essay_id FROM ' .$this->table. ' WHERE essay_status="pub" AND essay_tag LIKE "%'.$wd.'%" ORDER BY essay_top DESC, essay_id DESC LIMIT '.$offset.','.$limit;
            $dbData = DB::select($sql);//->toArray();   
            
            foreach ($dbData as $val) {
                $val =  get_object_vars($val);
                $ids_arr[] = $val['essay_id'];
            }
        }
        else {
            $dbData = Essay::where('essay_status', 'pub')
                        ->where('essay_categoryid', $cid)
                        // ->whereIn('essay_id', $ids)
                        ->orderBy('essay_top', 'desc') // 优先以置顶倒序排前面
                        ->orderBy('essay_id', 'desc') //再以id倒序排前面
                        
                        ->select('essay_id')
                        ->skip($offset)
                        ->take($limit)
                        // ->with('attachment')
                        ->get();
            foreach ($dbData as $val) {
                $ids_arr[] = $val['essay_id'];
            }
        }

        if(!isset($ids_arr) || empty($ids_arr)) {
            return false;
        }   

        foreach ($ids_arr as $key => $value) {
            $ess_data = Essay::getDetail(trim($value), $did);
            if ($ess_data) { // 过滤某些essay id不存在或者处于未发布状态下，进行容错
                $ess_data['essay_content'] = ''; //将list中的正文部分去掉，太占大小
                $tplData[] = $ess_data;
            }
            continue;
        }           

        return $tplData;

    }


    /**
     * Get recommend List
     *
     * @var array
     */
    public function getRecommend($offset, $limit, $eid, $did = 0) {

        if(!isset($eid) || empty($eid)) {
            return false;
        } 

        $essayData = Essay::getDetail($eid, $did);
        $cid = $essayData['essay_categoryid'];

        $recmdData = DB::select('select essay_id from ' .$this->table. ' where essay_categoryid='.$cid.' and essay_status="pub" order by rand() limit '.$limit);

        if(!isset($recmdData) || empty($recmdData)) {
            return false;
        }   
        $tplData = array();
        foreach ($recmdData as $value) {
            $value = get_object_vars($value);
            $essay_id = $value['essay_id'];
            if ($essay_id !== $eid) {
                $ess_data = Essay::getDetail($essay_id, $did);
                $ess_data['essay_content'] = ''; //将list中的正文部分去掉，太占大小
                $tplData[] = $ess_data;
            }
        }

        return $tplData;

    }


    /**
     * Get search List
     *
     * @var array
     */
    public function getSearch($offset, $limit, $wd, $did = 0) {

        if(!isset($wd) || empty($wd)) {
            return false;
        } 

        $res = DB::select('select essay_id, essay_title from ' .$this->table. ' where essay_status="pub" and (essay_title like "%'.$wd.'%" or essay_tag like "%'.$wd.'%")  order by essay_id desc limit '.$offset.','.$limit);

        if(!isset($res) || empty($res)) {
            return false;
        }   

        foreach ($res as $key => $value) {
            $value = get_object_vars($value);
            $value['essay_title'] = str_replace($wd, '<em>'.$wd.'</em>', $value['essay_title']);
            // var_dump($value);
            $tplData[$key] = $value;
        }           

        return $tplData;

    }



    /**
     * Get Essay detail
     *
     * @var array
     */
    public function getDetail($id, $did = 0) {

        $bnfObj =  Essay::where('essay_id', $id);
        $tplData = $bnfObj
                    ->where('essay_status', 'pub')
                    // 方便预览，暂不需要限制pid
                    // ->where('essay_categoryid', env('ANYCMS_YMCID', 123))
                    // ->attachment()
                    // ->with('attachment.attachment_path')
                    // ->orderBy('essay_id', 'desc')
                    ->first();

        
        // $bnfObj->increment('essay_click', 1);

        if(!isset($tplData) || empty($tplData)) {
            return false;
        }   

        // 获取当前did用户是否点过赞
        $tplData['like_status'] = 0;
        $tplData['vote_status'] = 0;

        if ($did !== 0) {
            $like_row = Likes::where('like_did', $did)
                                    ->where('like_essayid', $id)
                                    ->first();
            if ($like_row) {                        
                $tplData['like_status'] = $like_row['like_status'];    
            }

            $vote_row = Votes::where('vote_did', $did)
                                    ->where('vote_essayid', $id)
                                    ->first();
            if ($vote_row) {   
                $tplData['vote_status'] = $vote_row['vote_status'];    
            }                
        }   

        // 如果没有 Quote引用 内容，则截取正文内容
        if (empty($tplData['essay_quote'])) {
            $tplData['essay_quote'] = trim(mb_substr(strip_tags($tplData['essay_content']), 0, 100));
        }

        // 自动增加正文区域里图片地址的域名前缀
        $tplData['essay_content'] = str_replace('src="/uploadfile/image/', 'src="'.env('ANYCMS_SITEURL', '').'/uploadfile/image/', $tplData['essay_content']);
        
        // 将时间串，转换为unix时间戳再做处理: 2015-11-07 01:45:21 -> 11-07 01:45
        $tplData['essay_updatetime'] = date("m-d H:i", strtotime($tplData['essay_updatetime']));

        /*$tplData['attachment'] =  $bnfObj
                    ->attachment()
                    // ->with('attachment.attachment_path')
                    ->orderBy('attachment_id', 'desc')
                    ->first();  
        */
        // 增加详情页头图的信息
        $tplData['attachment'] = Attachments::where('attachment_essayid', $id)
                            ->orderBy('attachment_id', 'desc')
                            ->first();  
        if (isset($tplData['attachment']) && !empty($tplData['attachment'])) {
            $url_ori = $tplData['attachment']['attachment_path'];   
            $url_full = env('ANYCMS_SITEURL', '').$url_ori;
            $tplData['attachment']['attachment_path'] = $url_full;
            //env('ANYCMS_YMCID', 'http://anycms.licaimofang.com')
        }
        else {
            // $tplData['attachment'] = array();
        }

        return $tplData;

    }


    /**
     * Get unread Essay number by last check time
     *
     * @var array
     */
    public function getUnreadEssayNum($lastTime) {
        // $tplData =  BenefitsModel::all();
        $tplData =  Essay::where('essay_status', 'pub')
                    ->where('essay_categoryid', env('ANYCMS_YMCID', 123))   
                    ->where('essay_createtime', '>', $lastTime)             
                    ->get();
        //var_dump($tplData);
        return count($tplData);
    }


    /**
     * Get Category List
     *
     * @var array
     */
    public function getCateList($pid = 12, $offset = 0, $limit = 50) {

        $tplData = Categories::where('categories_status', 1) // 栏目显示与否，只列出允许显示的栏目
                    ->where('categories_parentid', $pid)
                    // ->orderBy('essay_id', 'asc') //desc
                    ->orderBy('categories_sort', 'asc')
                    ->skip($offset)
                    ->take($limit)
                    // ->with('attachment')
                    ->get();

        if(!isset($tplData) || empty($tplData)) {
            return false;
        }
        return $tplData;
    }

}


class Attachments extends Model {

    // use Authenticatable, CanResetPassword;

    protected $connection = 'lanycms';

    protected $primaryKey = 'attachment_id';
    
    public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'any_attachment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = ['attachment_id', 'attachment_path', 'attachment_essayid'];
    protected $visible = ['attachment_id', 'attachment_path', 'attachment_title', 'attachment_essayid'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    // protected $hidden = ['essay_status', 'essay_content','essay_password','essay_publisher','essay_editor'];

    protected $appends = ['is_admin'];

    public function getIsAdminAttribute() {

        return $this->attributes['admin'] == 'yes';
    }

}


class Categories extends Model {

    // use Authenticatable, CanResetPassword;

    protected $connection = 'lanycms';

    protected $primaryKey = 'categories_id';
    
    public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'any_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $visible = ['categories_id', 'categories_name', 'categories_englishname'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    // protected $hidden = ['essay_status', 'essay_content','essay_password','essay_publisher','essay_editor'];

}

