<?php


use App\Http\Controllers\CategoryChannelController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\ChannelGroupController;
use App\Http\Controllers\ChannelUserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamUserController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\AbsenceRequestController;
use App\Http\Middleware\AuthStore;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FacebookDetailController;
use App\Http\Controllers\GrowthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TikTokDetailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\YouTubeDetailController;

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

Route::post('auth/confirm-email', [AuthController::class, 'confirmEmail']);
Route::get('auth/send-email', [AuthController::class, 'sendRegisterMail']);
Route::get('auth/expired-time', [AuthController::class, 'checkExpiredTime']);
Route::post('auth/register', [AuthController::class, 'createUser']);
Route::post('auth/login', [AuthController::class, 'loginUser']);
Route::middleware(['auth:sanctum', AuthStore::class])->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('groups', GroupController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('teams', TeamController::class);
    Route::resource('platforms', PlatformController::class);
    Route::resource('channels', ChannelController::class);
    Route::resource('category-channel', CategoryChannelController::class);
    Route::resource('channel-group', ChannelGroupController::class);
    Route::resource('channel-user', ChannelUserController::class);
    Route::resource('team-user', TeamUserController::class);
    Route::post('permission/assign', [PermissionController::class, 'assignPermissionToRole']);
    Route::post('role/assign', [RoleController::class, 'assignRoleToUser']);
    Route::put('users/{id}/salary', [UserController::class, 'updateSalary']);
    Route::put('users/', [UserController::class, 'updateUser']);
    Route::resource('absence-types', AbsenceController::class);
    Route::resource('absence-request', AbsenceRequestController::class);

    Route::post('absence-request/answer', [AbsenceRequestController::class, 'answerRequest']);

    Route::resource('channel/facebook/growth', FacebookDetailController::class);
    Route::resource('channel/youtube/growth', YouTubeDetailController::class);
    Route::resource('channel/tiktok/growth', TikTokDetailController::class);
});
