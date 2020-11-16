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

        $this->createControllers();

        $this->createRequests();

        $this->info('Done.');
    }

    /**
     * @return void
     */
    protected function createModels() : void{
        $files = [];
        $this->listDir(__DIR__.'/../Stubs/Models', $files);
        foreach ($files as $file) {
            $dir = basename(dirname($file));

            $this->makeDir($dir);

            $filename = pathinfo($file, PATHINFO_FILENAME);

            $model = app_path("Models/{$filename}.php");

            $stub_model = $this->laravel['files']->get($file);

            $this->laravel['files']->put(
                $model,
                str_replace(
                    'DummyNamespace',
                    'App\\Models',
                    $stub_model
                )
            );
            $this->line('<info>'.$filename.' file was created:</info> '.str_replace(base_path(), '', $model));
        }
    }

    /**
     * @return void
     */
    protected function createControllers() : void{
        $files = [];
        $this->listDir(__DIR__.'/../Stubs/Controllers', $files);
        foreach ($files as $file) {
            $this->makeDir('Http/Controllers/Task');

            $filename = pathinfo($file, PATHINFO_FILENAME);

            $controller = app_path("Http/Controllers/Task/{$filename}.php");

            $stub_controller = $this->laravel['files']->get($file);

            $use_base_controller = file_exists(app_path('Http/Controllers/BaseController.php'))
                ? 'use App\\Http\\Controllers\\BaseController'
                : 'use Szkj\\Rbac\\Controllers\\BaseController';

            $use_transformer = file_exists(app_path('Http/Transformers/BaseTransformer.php'))
                ? 'use App\\Http\\Transformers\\BaseTransformer'
                : 'use Szkj\\Rbac\\Transformers\\BaseTransformer';

            $use_model = file_exists(app_path('Http/Models/Task.php'))
                ? 'use App\\Http\Models\\Task'
                : 'use Szkj\\Collection\\Models\\Task';

            $use_request = file_exists(app_path('Http/Request/Task/TaskStoreRequest.php'))
                ? 'use App\\Http\Requests\\Task\TaskStoreRequest'
                : 'use Szkj\Collection\Requests\Task\TaskStoreRequest';
            $this->laravel['files']->put(
                $controller,
                str_replace(
                    ['DummyNamespace', 'DummyControllerNamespace','DummyTransformersNamespace','DummyModelNamespace','DummyRequestsNamespace'],
                    ['App\\Http\\Controllers\\Task', $use_base_controller,$use_transformer,$use_model,$use_request],
                    $stub_controller
                )
            );
            $this->line('<info>'.$filename.' file was created:</info> '.str_replace(base_path(), '', $controller));
        }
    }

    protected function createRequests() : void{
        $this->makeDir('Http/Requests');
        $files = [];
        $this->listDir(__DIR__.'/../Stubs/Requests', $files);
        foreach ($files as $file) {
            $dir = basename(dirname($file));

            $this->makeDir('Http/Requests/'.$dir);

            $filename = pathinfo($file, PATHINFO_FILENAME);

            $request = app_path("Http/Requests/{$dir}/{$filename}.php");

            $stub_request = $this->laravel['files']->get($file);

            $use_base_request = file_exists(app_path('Http/Requests/BaseRequest.php'))
                ? 'use App\\Http\\Requests\\BaseRequest'
                : 'use Szkj\\Rbac\\Requests\\BaseRequest';

            $this->laravel['files']->put(
                $request,
                str_replace(
                    ['DummyNamespace', 'DummyUseNamespace'],
                    ["App\\Http\\Requests\\{$dir}", $use_base_request],
                    $stub_request
                )
            );
            $this->line('<info>'.$filename.' file was created:</info> '.str_replace(base_path(), '', $request));
        }
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
        return $this->laravel['files']->get(__DIR__ . "/../Stubs/$name.stub");
    }

    /**
     * @param $directory
     * @param array &$file
     */
    protected function listDir($directory, array &$file)
    {
        $temp = scandir($directory);
        foreach ($temp as $k => $v) {
            if ('.' == $v || '..' == $v) {
                continue;
            }
            $a = $directory.'/'.$v;
            if (is_dir($a)) {
                $this->listDir($a, $file);
            } else {
                array_push($file, $a);
            }
        }
    }

    /**
     * Make new directory.
     *
     * @param string $path
     */
    protected function makeDir($path = '')
    {
        $this->laravel['files']->makeDirectory(app_path().'/'.$path, 0755, true, true);
    }
}