<?php
/**
 * Creator htm
 * Created by 2020/11/2 14:50
 **/

namespace Szkj\Collection\Console;

use Illuminate\Console\Command;

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
        $this->createModels();

        $this->info('Done.');
    }

    /**
     * @return void
     */
    protected function createModels() : void{
        $this->info('move models');
        $model = $this->getStub('Models/Task');
        $this->laravel['files']->put(app_path() . '/Models', $model);
    }

    /**
     * Get stub contents.
     *
     * @param $name
     *
     * @return string
     */
    protected function getStub($name): string
    {
        return $this->laravel['files']->get(__DIR__ . "/stubs/$name.stub");
    }
}