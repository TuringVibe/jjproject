<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FinanceDashboardController;
use App\Http\Controllers\FinanceLabelController;
use App\Http\Controllers\FinanceMutationController;
use App\Http\Controllers\FinanceMutationScheduleController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectFileController;
use App\Http\Controllers\ProjectLabelController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\ProjectDashboardController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskFileController;
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
    if(auth()->check()) {
        switch(auth()->user()->role){
            case "user":
                return redirect(route("projects.list"));
            break;
            case "admin":
                return redirect(route("project-dashboard.dashboard"));
            break;
        }
    }
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

    Route::prefix('projects')->name('projects.')->group(function() {
        Route::get('/list',[ProjectController::class, "list"])->name('list');
        Route::get('/data',[ProjectController::class, "data"])->name('data');
        Route::get('/edit',[ProjectController::class, "edit"])->name('edit');
        Route::get('/detail',[ProjectController::class, "detail"])->name('detail');
        Route::post('/save',[ProjectController::class, "save"])->name('save');
        Route::post('/delete',[ProjectController::class, "delete"])->name('delete');
        Route::get('/board',[ProjectController::class, "board"])->name('board');
    });

    Route::prefix('project-files')->name('project-files.')->group(function() {
        Route::get('/data',[ProjectFileController::class, "data"])->name('data');
        Route::post('/save',[ProjectFileController::class, "save"])->name('save');
        Route::post('/delete',[ProjectFileController::class, "delete"])->name('delete');
        Route::get('/download',[ProjectFileController::class, "download"])->name('download');
    });

    Route::prefix('project-milestones')->name('project-milestones.')->group(function () {
        Route::get('/data',[MilestoneController::class, "data"])->name('data');
        Route::get('/edit',[MilestoneController::class, "edit"])->name('edit');
        Route::post('/save',[MilestoneController::class, "save"])->name('save');
        Route::post('/delete',[MilestoneController::class, "delete"])->name('delete');
    });

    Route::prefix('tasks')->name('tasks.')->group(function() {
        Route::get('/list',[TaskController::class, 'list'])->name('list');
        Route::get('/data',[TaskController::class, 'data'])->name('data');
        Route::get('/cards',[TaskController::class, 'cards'])->name('cards');
        Route::get('/card',[TaskController::class, 'card'])->name('card');
        Route::get('/edit',[TaskController::class, 'edit'])->name('edit');
        Route::post('/save',[TaskController::class, 'save'])->name('save');
        Route::post('/delete',[TaskController::class, 'delete'])->name('delete');
        Route::post('/move',[TaskController::class, 'move'])->name('move');
    });

    Route::prefix('task-files')->name('task-files.')->group(function() {
        Route::get('/data',[TaskFileController::class, "data"])->name('data');
        Route::post('/save',[TaskFileController::class, "save"])->name('save');
        Route::post('/delete',[TaskFileController::class, "delete"])->name('delete');
        Route::get('/download',[TaskFileController::class, "download"])->name('download');
    });

    Route::prefix('task-comments')->name('task-comments.')->group(function() {
        Route::get('/data',[TaskCommentController::class, "data"])->name('data');
        Route::post('/save',[TaskCommentController::class, "save"])->name('save');
        Route::post('/delete',[TaskCommentController::class, "delete"])->name('delete');
        Route::get('/edit',[TaskCommentController::class, "edit"])->name('edit');
    });

    Route::prefix('subtasks')->name('subtasks.')->group(function() {
        Route::get('/data',[SubtaskController::class, "data"])->name('data');
        Route::post('/save',[SubtaskController::class, "save"])->name('save');
        Route::post('/bulk-insert',[SubtaskController::class, "bulkInsert"])->name('bulk-insert');
        Route::post('/delete',[SubtaskController::class, "delete"])->name('delete');
        Route::get('/edit',[SubtaskController::class, "edit"])->name('edit');
    });

    Route::middleware(['admin'])->group(function() {
        Route::prefix('project-dashboard')->name('project-dashboard.')->group(function(){
            Route::get('/', [ProjectDashboardController::class, "dashboard"])->name('dashboard');
        });

        Route::prefix('project-labels')->name('project-labels.')->group(function(){
            Route::get('/list', [ProjectLabelController::class, "list"])->name('list');
            Route::get('/data', [ProjectLabelController::class, "data"])->name('data');
            Route::get('/detail', [ProjectLabelController::class, "detail"])->name('detail');
            Route::post('/save', [ProjectLabelController::class, "save"])->name('save');
            Route::post('/delete', [ProjectLabelController::class, "delete"])->name('delete');
        });

        Route::prefix('calendar')->name('events.')->group(function() {
            Route::get('/', [EventController::class, "list"])->name("list");
            Route::get('/data', [EventController::class, "data"])->name("data");
            Route::get('/edit', [EventController::class, "edit"])->name("edit");
            Route::post('/save', [EventController::class, "save"])->name("save");
            Route::post('/delete', [EventController::class, "delete"])->name("delete");
        });

        Route::prefix('finance-dashboard')->name('finance-dashboard.')->group(function() {
            Route::get('/',[FinanceDashboardController::class, 'dashboard'])->name('dashboard');
            Route::get('/data-by-label', [FinanceDashboardController::class, "dataByLabel"])->name('data-by-label');
            Route::get('/periodic-statistic', [FinanceDashboardController::class, "periodicStatistic"])->name('periodic-statistic');
        });

        Route::prefix('finance-mutations')->name('finance-mutations.')->group(function() {
            Route::get('/list',[FinanceMutationController::class, "list"])->name('list');
            Route::get('/data',[FinanceMutationController::class, "data"])->name('data');
            Route::get('/edit',[FinanceMutationController::class, "edit"])->name('edit');
            Route::post('/save',[FinanceMutationController::class, "save"])->name('save');
            Route::post('/delete',[FinanceMutationController::class, "delete"])->name('delete');
            Route::prefix('/scheduled')->name('scheduled.')->group(function() {
                Route::get('/data',[FinanceMutationScheduleController::class, "data"])->name('data');
                Route::get('/edit',[FinanceMutationScheduleController::class, "edit"])->name('edit');
                Route::post('/save',[FinanceMutationScheduleController::class, "save"])->name('save');
                Route::post('/delete',[FinanceMutationScheduleController::class, "delete"])->name('delete');
            });
        });

        Route::prefix('finance-labels')->name('finance-labels.')->group(function() {
            Route::get('/list',[FinanceLabelController::class, "list"])->name('list');
            Route::get('/data',[FinanceLabelController::class, "data"])->name('data');
            Route::get('/detail',[FinanceLabelController::class, "detail"])->name('detail');
            Route::post('/save',[FinanceLabelController::class, "save"])->name('save');
            Route::post('/delete',[FinanceLabelController::class, "delete"])->name('delete');
        });

        Route::prefix('users')->name('users.')->group(function() {
            Route::get('/list', [UserController::class, "list"])->name('list');
            Route::get('/data', [UserController::class, "data"])->name('data');
            Route::get('/detail', [UserController::class, "detail"])->name('detail');
            Route::post('/save', [UserController::class, "save"])->name('save');
            Route::post('/delete', [UserController::class, "delete"])->name('delete');
        });
    });
});
