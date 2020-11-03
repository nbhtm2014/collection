<?php
/**
 * Creator htm
 * Created by 2020/11/2 13:52
 **/
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionTable extends Migration
{

    /**
     * @return string
     */
    public function getConnection() :string
    {
        return config('database.default');
    }

    /**
     * @return string
     */
    public function getPrefix() : string
    {
        return config('database.connections.'.$this->getConnection().'.prefix');
    }

    /**
     * @param string $name
     * @return string
     */
    public function tableName(string $name):string{
        return $name;
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * create tasks table
         */
        Schema::create($this->tableName('tasks'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title',255)->comment('采集标题');
            $table->string('platform_id',100)->comment('采集平台id');
            $table->text('keywords')->comment('采集关键词');
            $table->tinyInteger('status')->default(0)->comment('任务状态');
            $table->integer('user_id')->comment('用户id');
            $table->tinyInteger('pull_status')->default(0)->comment('推送状态');
            $table->string('pcd',100)->comment('省市区');
            $table->tinyInteger('type')->default(0)->comment('任务类型');
            $table->string('system_id',255)->nullable()->comment('系统id');
            $table->string('es_id',255)->nullable()->comment('es_id');

            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * create platforms table
         */
        Schema::create($this->tableName('platforms'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',255)->comment('平台名称');
            $table->string('cate',255)->comment('平台分类');
            $table->timestamps();
        });


        /**
         * create entities tables
         */
        Schema::create($this->tableName('entities'),function (Blueprint $table){
            $table->bigIncrements('id');


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
        Schema::dropIfExists($this->tableName('tasks'));
        Schema::dropIfExists($this->tableName('platforms'));
        Schema::dropIfExists($this->tableName('entities'));
    }

}