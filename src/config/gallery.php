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
            'region'      => 'sh',
            'credentials' => [
                'secretId'  => 'AKIDbFnBPV9N6g8PYY91TkJp6D92pT0NIpoc',
                'secretKey' => 'mOp9MRMF2eN0Tq26zhEaZO3XsJk3CA1i',
            ],
            'bucket'      => 'kemanyun-1251581441',
            'base_url'    => 'https://img.kemanyun.com',
        ],

        'aliyun' => [

        ],
    ],
];
