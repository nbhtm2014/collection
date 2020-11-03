<?php
/**
 * Creator htm
 * Created by 2020/11/2 14:50
 **/

namespace Szkj\Collection\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'szkj:collection-install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the collection package';

    /**
     * Install directory.
     *
     * @var string
     */
    protected $directory = '';

    /**
     * @return string
     */
    public function getConnection(): string
    {
        return config('database.default');
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $log = <<<ETO
        ███████╗     ███████╗    ██╗  ██╗         ██╗
        ██╔════╝     ╚══███╔╝    ██║ ██╔╝         ██║
        ███████╗     ███╔╝       █████╔╝          ██║
        ╚════██║     ███╔╝       ██╔═██╗     ██   ██║
        ███████║     ███████╗    ██║  ██╗    ╚█████╔╝
        ╚══════╝     ╚══════╝    ╚═╝  ╚═╝     ╚════╝
ETO;
        $this->info($log);
        if (!Schema::hasTable('platforms')) {
            $this->error('Please run migrate!!');
            die();
        }
        $platforms = $this->choice('请选择采集平台（多选请用逗号隔开,比如0,1,2）',
            ['电商平台', '微信公众号', '服务平台'],
            0,
            null,
            true);
        $this->info('creating tables....');
        foreach ($platforms as $k => $v) {
            if (hash_equals($v, '电商平台')) {
                $this->items($v);
            }
            if (hash_equals($v, '微信公众号')) {
                $this->wechat();
            }
            if (hash_equals($v, '服务平台')) {
                $this->service();
            }
        }
    }

    /**
     * @param string $name
     * @return void
     */
    public function items(string $name) : void
    {
        if (!Schema::hasTable('items')) {
            Schema::create('items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('task_id')->default(0)->comment('任务id');
                $table->string('batch', 50)->nullable()->comment('批号');
                $table->string('keyword', 50)->nullable()->comment('关键词');
                $table->string('nid', 50)->comment('商品id');
                $table->string('category_id', 200)->nullable()->comment('原始分类id');
                $table->string('title', 500)->comment('标题');
                $table->string('item_loc_prov', 50)->nullable()->comment('发货地(省)');
                $table->string('item_loc_city', 50)->nullable()->comment('发货地(市)');
                $table->string('shop_id', 50)->comment('店铺id');
                $table->double('view_price')->nullable()->comment('列表价格');
                $table->integer('view_sales')->nullable()->comment('显示销量');
                $table->integer('comment_count')->nullable()->comment('评论数量');
                $table->integer('platform_id')->comment('平台');
                $table->json('property')->nullable()->comment('属性');
                $table->double('view_amount')->nullable()->comment('总销售额');
                $table->string('nick', 255)->nullable();
                $table->string('seller_id', 255)->nullable();
                $table->string('classify', 255)->nullable()->comment('公司分类');

                $table->timestamps();
            });
        }
        $insert = [
            ['id' => 1, 'name' => '天猫', 'cate' => $name, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => '淘宝', 'cate' => $name, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => '苏宁易购', 'cate' => $name, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => '京东', 'cate' => $name, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => '阿里巴巴', 'cate' => $name, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::connection($this->getConnection())->table('platforms')->insert($insert);
    }

    public function wechat()
    {

    }

    public function service()
    {

    }
}