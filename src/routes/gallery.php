<?php
/**
 * Created by PhpStorm.
 * User: yanghui
 * Date: 2018/5/21
 * Time: 14:03
 */
Route::group(['prefix' => 'fenmob','namespace' => 'Fenmob\\Gallery\\Controllers'],function (){
    Route::post('gallery/create_category',"GalleryController@createCategory");
    Route::post('gallery/update_category',"GalleryController@updateCategory");
    Route::get("gallery/delete_category","GalleryController@deleteCategory");
    Route::get('gallery/move_file',"GalleryController@moveFile");
    //恢复文件
    Route::get('gallery/recover_file',"GalleryController@recoverFile");
    Route::resource('gallery','GalleryController');
});