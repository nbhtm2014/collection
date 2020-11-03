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
        $this->registerMigrations();
        $this->registerPublishing();
        $this->registerRoutes();
    }

    public function register()
    {
        $this->commands($this->commands);
    }

    /**
     *
     * @return void
     */
    protected function registerPublishing()
    {
        $this->publishes([__DIR__ . '/../../config' => config_path()], 'szkj-collection-config');
    }

    /**
     * 表迁移
     */
    public function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }


    /**
     * 注册路由
     */
    public function registerRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
    }
}