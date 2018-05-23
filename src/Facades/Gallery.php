<?php
/**
 * Created by PhpStorm.
 * User: yanghui
 * Date: 2018/5/23
 * Time: 10:57
 */
namespace Fenmob\Gallery\Facades;

use Illuminate\Support\Facades\Facade;

class Gallery extends Facade{
    /**
     * @return string
     */
    protected static function getFacadeAccessor() { return 'gallery'; }
}