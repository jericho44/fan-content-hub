<?php

use App\Http\Controllers\ApiWeb;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['auth:sanctum']], function () {

    // Master
    Route::group(['prefix' => 'master'], function () {
        // User
        Route::group(['prefix' => 'user'], function () {
            Route::get('/', [ApiWeb\MasterUserController::class, 'index'])->middleware(['role:developer,admin']);
            Route::post('/', [ApiWeb\MasterUserController::class, 'store']);
            Route::get('/{id}', [ApiWeb\MasterUserController::class, 'show'])->middleware(['role:developer,admin'])->whereUuid('id');
            Route::put('/{id}', [ApiWeb\MasterUserController::class, 'update'])->middleware(['role:developer,admin'])->whereUuid('id');
            Route::put('/{id}/change-status', [ApiWeb\MasterUserController::class, 'changeStatus'])->middleware(['role:developer,admin'])->whereUuid('id');
            Route::put('/{id}/reset-password', [ApiWeb\MasterUserController::class, 'resetPassword'])->middleware(['role:developer,admin'])->whereUuid('id');
        });
    });

    // Dashboard
    Route::group([
        'prefix' => 'dashboard',
    ], function () {
        Route::get('/', [ApiWeb\DashboardController::class, 'index'])->middleware(['role:,admin,structural']);
    });

    // Fan Content Hub Admin (CMS)
    Route::group(['prefix' => 'events'], function () {
        Route::get('/', [ApiWeb\EventController::class, 'index']);
        Route::post('/', [ApiWeb\EventController::class, 'store']);
        Route::get('/{idHash}', [ApiWeb\EventController::class, 'show']);
        Route::put('/{idHash}', [ApiWeb\EventController::class, 'update']);
        Route::delete('/{idHash}', [ApiWeb\EventController::class, 'destroy']);
    });

    Route::group(['prefix' => 'tags'], function () {
        Route::get('/', [ApiWeb\TagController::class, 'index']);
        Route::post('/', [ApiWeb\TagController::class, 'store']);
        Route::get('/{idHash}', [ApiWeb\TagController::class, 'show']);
        Route::put('/{idHash}', [ApiWeb\TagController::class, 'update']);
        Route::delete('/{idHash}', [ApiWeb\TagController::class, 'destroy']);
    });

    Route::group(['prefix' => 'contents'], function () {
        Route::get('/', [ApiWeb\ContentController::class, 'index']);
        Route::post('/', [ApiWeb\ContentController::class, 'store']);
        Route::get('/{idHash}', [ApiWeb\ContentController::class, 'show']);
        Route::put('/{idHash}', [ApiWeb\ContentController::class, 'update']);
        Route::delete('/{idHash}', [ApiWeb\ContentController::class, 'destroy']);
    });
});
