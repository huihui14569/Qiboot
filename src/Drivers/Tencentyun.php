<?php
/**
 * Created by PhpStorm.
 * User: yanghui
 * Date: 2018/5/23
 * Time: 10:32
 */

namespace Fenmob\Gallery\Drivers;

use Qcloud\Cos\Client;
use QcloudApi;

class Tencentyun implements uploadInterface {
    private $client;

    public function __construct() {
        $this->client = new Client([
            'region'      => config('gallery.upload_driver.tencentyun.region'),
            'credentials' => config('gallery.upload_driver.tencentyun.credentials'),
        ]);
    }

    /**
     * 功能:上传图片到腾讯云OSS
     * 函数名:upload
     * 作者:杨慧
     * @return mixed
     */
    public function upload() {
        $shop_id = config('gallery.shop_id');
        $file = request()->file(config('gallery.file_field'));
        $key = "{$shop_id}/".date('Y/m/').md5($file->getClientOriginalName()).".".$file->getClientOriginalExtension();
        $bucket = config('gallery.upload_driver.tencentyun.bucket');
        $this->client->Upload($bucket,$key,file_get_contents($file->getRealPath()));
        //如果有独立域名
        if (config('gallery.upload_driver.tencentyun.base_url')){
            $key = config('gallery.upload_driver.tencentyun.base_url')."/".$key;
        }else{
            $key = $this->client->getObjectUrl($bucket, $key);
        }
        return [
            'path' => $key
        ];
    }
}