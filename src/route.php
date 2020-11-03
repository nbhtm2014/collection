<?php
/**
 * Creator htm
 * Created by 2020/11/3 16:44
 **/
use Illuminate\Support\Facades\Route;
use Szkj\Collection\Controllers\TaskController;

Route::prefix('api/collection')->middleware(['auth:api', 'szkj.rbac'])->group(function () {
    Route::apiResource('task', TaskController::class);
});