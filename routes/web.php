<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if(auth()->check())
        return redirect(route("project.dashboard"));
    return redirect(route("login"));
});

Route::middleware(['guest'])->group(function() {
    Route::get("/login", [AuthenticationController::class, "login"])->name("login");
    Route::post("/login", [AuthenticationController::class, "doLogin"])->name("login.do");
    Route::prefix('/password')->name('password.')->group(function() {
        Route::get("/forget", [AuthenticationController::class, "forgetPassword"])->name("forget");
        Route::post("/forget", [AuthenticationController::class, "sendLinkResetPassword"])->name("link");
        Route::get("/reset", [AuthenticationController::class, "resetPassword"])->name("reset");
        Route::post("/reset", [AuthenticationController::class, "doResetPassword"])->name("reset.do");
    });
});

Route::middleware(['auth'])->group(function() {
    Route::post('/logout', [AuthenticationController::class, "logout"])->name("logout");
    Route::prefix('project')->name('project.')->group(function() {
        Route::get('/dashboard', [ProjectController::class, "dashboard"])->name('dashboard');
        Route::get('/list',[ProjectController::class, "list"])->name('list');
        Route::get('/tasks',[])->name('tasks');
        Route::get('/labels',[])->name('labels');
    });

    Route::prefix('finance')->name('finance.')->group(function() {
        Route::get('/dashboard', [])->name('dashboard');
        Route::get('/mutations',[])->name('mutations');
        Route::get('/labels',[])->name('labels');
    });

    Route::prefix('users')->name('users.')->group(function() {
        Route::get('/list', [UserController::class, "list"])->name('list');
        Route::get('/data', [UserController::class, "data"])->name('data');
        Route::get('/detail', [UserController::class, "detail"])->name('detail');
        Route::post('/save', [UserController::class, "save"])->name('save');
        Route::post('/delete', [UserController::class, "delete"])->name('delete');
    });
});
