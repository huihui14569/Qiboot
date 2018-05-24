<?php
/**
 * Created by PhpStorm.
 * User: yanghui
 * Date: 2018/5/23
 * Time: 09:46
 */

return [
    'default' => 'tencentyun', //默认腾讯云

    'file_field' => 'gallery_file',

    'shop_id' => 1,//可以再

    'upload_driver' => [
        'tencentyun' => [
            'classname'   => \Fenmob\Gallery\Drivers\Tencentyun::class,
            'region'      => '',
            'credentials' => [
                'secretId'  => '',
                'secretKey' => '',
            ],
            'bucket'      => '',
            'base_url'    => '',
        ],

        'aliyun' => [

        ],
    ],
];
