<?php
/**
 * Creator htm
 * Created by 2020/11/3 16:44
 **/

use Szkj\Collection\Controllers\TaskController;

$api = app('Dingo\Api\Routing\Router');
$api->version(config('api.version'), ['middleware' => ['auth:api', 'szkj.rbac']], function ($api) {
    $api->group(['prefix' => 'collection'], function ($api) {
        $api->resource('task', TaskController::class);
    });
});