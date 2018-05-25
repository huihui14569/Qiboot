<?php
/**
 * Created by PhpStorm.
 * User: yanghui
 * Date: 2018/5/23
 * Time: 09:46
 */

return [
    'default' => 'tencentyun', //默认腾讯云

    'default_guard' => 'user',//默认认证的驱动

    'file_field' => 'gallery_file',

    'shop_id' => 0,//指定区分的字段

    'middleware' => function ($request, $next) {
        if (Auth::guard(config('gallery.default_guard'))->check()) {
            return $next($request);
        } else {
            return response()->json([
                'status_code' => 0,
                'msg'         => '请先登录',
                'status'      => 0,
            ]);
        }
    },

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
