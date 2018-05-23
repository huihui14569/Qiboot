<?php
/**
 * Created by PhpStorm.
 * User: yanghui
 * Date: 2018/5/21
 * Time: 11:55
 */
namespace Fenmob\Gallery;
use Illuminate\Support\ServiceProvider;

class GalleryServiceProvider extends ServiceProvider {
    public function boot(){
        $this->loadRoutesFrom(__DIR__.'/routes/gallery.php');
        //加载数据库迁移
        $this->loadMigrationsFrom(__DIR__."/migrations");
    }

    public function register(){
        $this->app->singleton('gallery',function(){
            return new Gallery();
        });
    }
}