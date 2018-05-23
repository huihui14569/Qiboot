<?php

namespace Fenmob\Gallery\Models;

use Illuminate\Database\Eloquent\Model;

class FileCategory extends Model
{
    protected $table = "file_category";
    protected $fillable = [
        'shop_id','name','sort','file_num','pid'
    ];

    /**
     * 功能:子类
     * 函数名:child
     * 作者:杨慧
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function child(){
        return $this->hasMany(self::class,'pid','id');
    }

    /**
     * 功能:关联文件
     * 函数名:files
     * 作者:杨慧
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function files(){
        return $this->morphMany(File::class,'filetable');
    }

    /**
     * 功能:父级分类
     * 函数名:parent
     * 作者:杨慧
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(){
        return $this->belongsTo(self::class,'id','pid');
    }
}
