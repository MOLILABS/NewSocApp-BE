<?php


use App\Http\Controllers\CategoryChannelController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\ChannelGroupController;
use App\Http\Controllers\ChannelUserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamUserController;
use App\Http\Middleware\AuthStore;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;

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

Route::post('auth/register', [AuthController::class, 'createUser']);
Route::post('auth/login', [AuthController::class, 'loginUser']);
Route::middleware(['auth:sanctum', AuthStore::class])->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('groups', GroupController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('teams', TeamController::class);
    Route::resource('channels', ChannelController::class);
    Route::resource('category-channel', CategoryChannelController::class);
    Route::resource('channel-group', ChannelGroupController::class);
    Route::resource('channel-user', ChannelUserController::class);
    Route::resource('team-user', TeamUserController::class);
    Route::post('permission/assign', [PermissionController::class, 'assignPermissionToRole']);
    Route::post('role/assign', [RoleController::class, 'assignRoleToUser']);
});
Route::get('test/tiktok', [ChannelController::class, 'getInfoTiktok']);
