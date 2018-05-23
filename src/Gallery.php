<?php
/**
 * Created by PhpStorm.
 * User: yanghui
 * Date: 2018/5/21
 * Time: 12:00
 */

namespace Fenmob\Gallery;


class Gallery {

    public function uploader(){
        $defaultUploader = config('gallery.default');
        $classname = config("gallery.upload_driver.{$defaultUploader}.classname");
        return new $classname();
    }
}