<?php

use App\Http\Controllers\Bill\BillInfoController;
use App\Cache\BillInfoCache;
use Illuminate\Support\Facades\Route;

Route::get('/addBillInfo', [BillInfoController::class, 'add']);
Route::any('/updataBillInfo', [BillInfoController::class, 'updata']);
Route::any('/deleteBillInfo', [BillInfoController::class, 'delete']);
Route::any('/queryBillInfo', [BillInfoController::class, 'query']);
Route::any('/likeBillInfo', [BillInfoController::class, 'setFavorite']);
Route::any('/dislikeBillInfo', [BillInfoController::class, 'unSetFavorite']);
Route::any('/queryLikeBillInfo', [BillInfoController::class, 'queryFavorite']);
Route::any('/RedisAdd', [BillInfoCache::class, 'add']);
Route::any('/hello', [BillInfoController::class, 'HelloWorld']);


Route::get('/', function () {
    return array(
        'error_code' => 0,
        'message' => '欢迎使用账单管理系统'
    );  
});
