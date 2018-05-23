<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_category', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_id')->nullable()->comment('店铺ID');
            $table->string('name',100)->nullable()->comment("分类名");
            $table->integer('sort')->nullable()->comment("排序,数值越大越靠前");
            $table->integer('file_num')->nullable()->comment("文件数量");
            $table->integer('pid')->default(0)->nullable()->comment("父级ID");
            $table->index('shop_id','index_shop_id');
            $table->unique(['shop_id','name'],'index_shop_id_name');
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
        Schema::table('file_category', function (Blueprint $table) {
            //
        });
    }
}
