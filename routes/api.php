<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [App\Http\Controllers\Api\Auth\LoginController::class, 'index']);

Route::group(['middleware' => 'auth:api'], function() {

    //logout
    Route::post('/logout',
    [App\Http\Controllers\Api\Auth\LoginController::class, 'logout']);

});

Route::prefix('admin')->group(function () {
    //group route with middleware "auth:api"
    Route::group(['middleware' => 'auth:api'], function () {
        //dashboard
        Route::get('/dashboard',
        App\Http\Controllers\Api\Admin\DashboardController::class);

        //permissions
        Route::get('/permissions', [\App\Http\Controllers\Api\Admin\PermissionController::class, 'index'])
        ->middleware('permission:permission.index');

        //permissions all
        Route::get('/permissions/all', [\App\Http\Controllers\Api\Admin\PermissionController::class, 'all'])
        ->middleware('permission:permission.index');

        //roles all
        Route::get('/roles/all', [\App\Http\Controllers\Api\Admin\RoleController::class, 'all'])
        ->middleware('permission:roles.index');

        Route::post('/image/{id}', [\App\Http\Controllers\Api\Admin\UserController::class, 'updateDokumen']);

        //roles
        Route::apiResource('/roles', \App\Http\Controllers\Api\Admin\RoleController::class)
        ->middleware('permission:roles.index|roles.store|roles.update|roles.delete');

        Route::apiResource('/users', \App\Http\Controllers\Api\Admin\UserController::class)
        ->middleware('permission:users.index');

        Route::apiResource('/sekolah', \App\Http\Controllers\Api\Admin\SchoolController::class)
        ->middleware('permission:school.index');

        Route::apiResource('/timeline', \App\Http\Controllers\Api\Admin\TimelineController::class);
        Route::apiResource('/jurnal', \App\Http\Controllers\Api\Admin\JurnalController::class);
        Route::post('/dokumenjurnal/{id}', [\App\Http\Controllers\Api\Admin\JurnalController::class, 'updateDokumen']);

        Route::apiResource('/blog', \App\Http\Controllers\Api\Admin\BlogController::class);
        Route::post('/dokumenblog/{id}', [\App\Http\Controllers\Api\Admin\BlogController::class, 'updateDokumen']);

       
    });
});
