<?php
/**
 * Creator htm
 * Created by 2020/11/3 16:44
 **/


$api = app('Dingo\Api\Routing\Router');
$api->version(config('api.version'), [
    'middleware' => config('szkj.route.middleware'),
    'namespace'  => config('szkj.route.namespace'),
],
    function ($api) {
        $api->group(['prefix' => 'collection'], function ($api) {
            $api->resource('task', 'TaskController');
        });
    });