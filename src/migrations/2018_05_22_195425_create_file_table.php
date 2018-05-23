<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_id')->nullable()->comment('店铺ID');
            $table->integer('cate_id_top')->nullable()->comment("一级分类ID");
            $table->integer('cate_id_sub')->nullable()->comment("二级分类ID");
            $table->string('name',100)->nullable()->comment("文件名");
            $table->string("old_name",100)->nullable()->comment("原文件名");
            $table->string("path")->nullable()->comment("文件路径");
            $table->integer('sort')->nullable()->comment("排序,数值越大越靠前");
            $table->integer('filetable_id')->nullable()->comment("关联表ID");
            $table->string('filetable_type',50)->nullable()->comment("关联表类型");
            $table->integer("show_gallery")->default(1)->nullable()->comment("1在图库里显示,0不在图库里显示");
            $table->index('shop_id','index_shop_id');
            $table->softDeletes();//软删除字段
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file', function (Blueprint $table) {
            //
        });
    }
}
