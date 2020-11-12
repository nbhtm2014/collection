<?php
/**
 * Creator htm
 * Created by 2020/11/2 15:11
 **/

namespace Szkj\Collection\Providers;


use Illuminate\Support\ServiceProvider;
use Szkj\Collection\Console\InstallCommand;

class CollectionServiceProvider extends ServiceProvider
{

    /**
     * @var array
     */
    protected $commands = [
        InstallCommand::class,
    ];

    /**
     * @return void
     */
    public function boot(): void
    {

    }

    public function register()
    {
        $this->registerRoutes();

        $this->commands($this->commands);
    }

    /**
     * 注册路由
     */
    public function registerRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
    }
}