<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $connection = 'weda';

    public $table = 'users';

    public $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


        /**
     * Get detail
     *
     * @var array
     */
    public function getDetail($id, $did = 0) {

        $bnfObj =  User::where('id', $id);
        $user = $bnfObj
                    // ->where('essay_status', 'pub')
                    // 方便预览，暂不需要限制pid
                    // ->where('essay_categoryid', env('ANYCMS_YMCID', 123))
                    // ->attachment()
                    // ->with('attachment.attachment_path')
                    // ->orderBy('essay_id', 'desc')
                    ->first();

    
        $tplData = array('user' => $user);


        if(!isset($tplData) || empty($tplData)) {
            return false;
        }   


        return $tplData;
    }


    /**
     * Get list
     *
     * @var array
     */
    public function getList($offset = 0) {

        $tplData = User::where('id', '>', 0)
            // ->where('essay_categoryid', $cid)
            // ->whereIn('essay_id', $ids)
            // ->orderBy('essay_top', 'desc') // 优先以置顶倒序排前面
            ->orderBy('id', 'desc') //再以id倒序排前面
            
            // ->select('essay_id')
            // ->skip($offset)
            // ->take($limit)
            // ->with('attachment')
            ->get();

        if(!isset($tplData) || empty($tplData)) {
            return false;
        }   


        return $tplData;
    }
}
