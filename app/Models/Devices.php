<?php 
/**
 * All about Essay-Devices Model (List and Detail)
 * author: enimo
 * date: 2015/5/4
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// use App\Models\Essay;
use Log;

class Devices extends Model {

	// use Authenticatable, CanResetPassword;

	protected $connection = 'lanycms';

    protected $primaryKey = 'id';
    
    public $timestamps = false;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'any_devices';


	/**
	 * Device List 
	 *
	 * 设备列表
	 *
	 */
    public function deviceList($offset = 0, $limit = 6)
    {
    	$voteObj = Devices::where('status', 1)->select('did','extra','ip','jp_regid'); //->select('extra');
		$list = $voteObj->orderBy('updatetime', 'desc')
							->skip($offset)
							->take($limit)
							->get();
		return $list;
    }


	/**
	 * Device register Handler 
	 *
	 * @var array
	 */
    public function register($info)
    {
		$did = $info['did'];
		$apikey = $info['apikey'];

		$deviceObj = Devices::where('did', $did);
		$row = $deviceObj->first();
		if (empty($row)) {
			// $ret = Devices::create($info);
			$ret = Devices::insertGetId($info);
		}
		//如果该did已经注册过，则修改apikey和updatetime，其他信息不可修改
		else {
			$ret = $deviceObj->update(['apikey' => $apikey, 'updatetime'=> time()]);
	
		}		
		$tplData = $ret;

		return $tplData;
    }


	/**
	 * 增加jpush的regid信息
	 *
	 * @var array
	 */
    public function addJpushRegid($did, $regid)
    {
		$deviceObj = Devices::where('did', $did); // ->where('jp_regid', $regid)
		$row = $deviceObj->first();
		// Log::info('存储jpush_regid, did='.$did.', regid='.$regid);
		if (empty($row)) {
			// did 信息查询不到
			Log::info('did信息查询不到, did='.$did);

			return false;
		}
		else {
			if($row['jp_regid'] == $regid) {
				//如果该did的regid已经存储过，则直接返回
				return true;
			}
			else {
				$ret = $deviceObj->update(['jp_regid' => $regid, 'updatetime'=> time()]);
				if ($ret) {
					return true;
				}
				else {
					Log::info('jp_regid信息更新失败');
					return false;
				}
			}
		}

    }

    /**
	 * 增加did用户的特征标签
	 *
	 * @var array
	 */
    public function addUserTag($did, $tagArr)
    {
		$deviceObj = Devices::where('did', $did); // ->where('jp_regid', $regid)
		$row = $deviceObj->first();
		// Log::info('存储jpush_regid, did='.$did.', regid='.$regid);
		if (empty($row)) {
			// did 信息查询不到
			Log::info('did信息查询不到, did='.$did);

			return false;
		}
		else {
			$oriTagArr = json_decode($row['user_tags'], true); // when true, convert to array
			if(!isset($oriTagArr['air'])) {
				$oriTagArr['air'] = array();
			}
			if(!isset($oriTagArr['hotel'])) {
				$oriTagArr['hotel'] = array();
			}
			if(!isset($oriTagArr['bank'])) {
				$oriTagArr['bank'] = array();
			}

			// 开始合并新tag
			if(isset($tagArr['bank'])) {
				$oriTagArr['bank'] = array_unique(array_merge($oriTagArr['bank'], $tagArr['bank']));
			}
			if(isset($tagArr['hotel'])) {
				$oriTagArr['hotel'] = array_unique(array_merge($oriTagArr['hotel'], $tagArr['hotel']));
			}
			if(isset($tagArr['air'])) {
				$oriTagArr['air'] = array_unique(array_merge($oriTagArr['air'], $tagArr['air']));
			}

			$newTagStr = json_encode($oriTagArr);

			if($row['user_tags'] == $newTagStr) {
				//如果tag无变化，则直接返回
				return true;
			}
			else {
				$ret = $deviceObj->update(['user_tags' => $newTagStr, 'updatetime'=> time()]);
				if ($ret) {
					return true;
				}
				else {
					Log::info('newTagStr信息更新失败');
					return false;
				}
			}
		}

    }


    /**
	 * 检验apikey的有效性
	 *
	 * @var array
	 */
    public function checkApikeyAvailable($did, $apikey)
    {
		$deviceObj = Devices::where('did', $did)->where('apikey', $apikey)->where('status', 1);
		$row = $deviceObj->first();
		if (empty($row)) {
			// 表明该apikey非法或者已经失效，或被禁用
			return false;
		}
	
		return true;
    }

}
