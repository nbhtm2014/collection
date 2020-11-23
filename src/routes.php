<?php
/**
 * Creator htm
 * Created by 2020/11/3 16:44
 **/


$api = app('Dingo\Api\Routing\Router');
$api->version(config('api.version'), [
    'middleware' => config('szkj.route.middleware')
],
    function ($api) {
        $api->group(['prefix' => 'collection', 'namespace'  => config('szkj.route.namespace.collection').'\\Task'], function ($api) {
            $api->resource('task', 'TaskController');
        });
    });