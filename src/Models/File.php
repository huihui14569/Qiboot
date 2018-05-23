<?php

namespace Fenmob\Gallery\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes;
    protected $table = "file";
    protected $fillable = [
        'shop_id','cate_id_top','cate_id_sub','name','old_name','path','sort','filetable_id','filetable_type','show_gallery'
    ];

    /**
     * 功能:图片文件多态关联
     * 函数名:filetable
     * 作者:杨慧
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function filetable(){
        return $this->morphTo();
    }
}
