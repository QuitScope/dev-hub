<?php

use Application\Analytics\Controllers\GetAnalyticsForecastController;
use Application\Analytics\Controllers\GetAnalyticsSnapshotsController;
use Application\Analytics\Controllers\GetAnalyticsSummaryController;
use Application\Analytics\Controllers\GetAnalyticsTrendsController;
use Application\Analytics\Controllers\RecalculateAnalyticsController;
use Application\Bugs\Controllers\DeleteBugController;
use Application\Bugs\Controllers\ListBugsController;
use Application\Bugs\Controllers\ShowBugController;
use Application\Bugs\Controllers\StoreBugController;
use Application\Bugs\Controllers\UpdateBugController;
use Application\Jira\Controllers\DeleteJiraNoteController;
use Application\Jira\Controllers\GetJiraSettingsController;
use Application\Jira\Controllers\ListJiraIssuesController;
use Application\Jira\Controllers\ShowJiraIssueController;
use Application\Jira\Controllers\StoreJiraNoteController;
use Application\Jira\Controllers\SyncJiraIssuesController;
use Application\Jira\Controllers\UpdateJiraNoteController;
use Application\Jira\Controllers\UpdateJiraSettingsController;
use Application\Snippets\Controllers\DeleteSnippetController;
use Application\Snippets\Controllers\ListSnippetsController;
use Application\Snippets\Controllers\ShowSnippetController;
use Application\Snippets\Controllers\StoreSnippetController;
use Application\Snippets\Controllers\UpdateSnippetController;
use Application\Todos\Controllers\DeleteTodoController;
use Application\Todos\Controllers\ListTodosController;
use Application\Todos\Controllers\ShowTodoController;
use Application\Todos\Controllers\StoreTodoController;
use Application\Todos\Controllers\UpdateTodoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes v1
|--------------------------------------------------------------------------
|
| Version 1 of the API routes. These routes are loaded by the RouteServiceProvider
| within a group which contains the "api" middleware group.
|
*/

// Snippets Resource Routes
Route::prefix('snippets')->name('snippets.')->group(function () {
    Route::get('/', ListSnippetsController::class)->name('index');
    Route::post('/', StoreSnippetController::class)->name('store');
    Route::get('{snippet}', ShowSnippetController::class)->name('show');
    Route::put('{snippet}', UpdateSnippetController::class)->name('update');
    Route::delete('{snippet}', DeleteSnippetController::class)->name('destroy');
});

// Todos Resource Routes
Route::prefix('todos')->name('todos.')->group(function () {
    Route::get('/', ListTodosController::class)->name('index');
    Route::post('/', StoreTodoController::class)->name('store');
    Route::get('{id}', ShowTodoController::class)->name('show'); // Keep {id} for consistency with controller
    Route::put('{id}', UpdateTodoController::class)->name('update'); // Keep {id} for consistency with controller
    Route::delete('{id}', DeleteTodoController::class)->name('destroy'); // Keep {id} for consistency with controller
});

// Jira Resource Routes
Route::prefix('jira')->name('jira.')->group(function () {
    // Jira Issues Routes
    Route::prefix('issues')->name('issues.')->group(function () {
        Route::get('/', ListJiraIssuesController::class)->name('index');
        Route::get('{jiraIssue}', ShowJiraIssueController::class)->name('show');
        Route::post('sync', SyncJiraIssuesController::class)->name('sync');
    });

    // Jira Notes Routes
    Route::prefix('notes')->name('notes.')->group(function () {
        Route::post('/', StoreJiraNoteController::class)->name('store');
        Route::put('{jiraNote}', UpdateJiraNoteController::class)->name('update');
        Route::delete('{jiraNote}', DeleteJiraNoteController::class)->name('destroy');
    });

    // Jira Settings Routes (Singleton Pattern - one per user)
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', GetJiraSettingsController::class)->name('show');
        Route::put('/', UpdateJiraSettingsController::class)->name('update');
    });
});

// Analytics Resource Routes
Route::prefix('analytics')->name('analytics.')->group(function () {
    Route::get('summary', GetAnalyticsSummaryController::class)->name('summary');
    Route::get('snapshots', GetAnalyticsSnapshotsController::class)->name('snapshots');
    Route::get('trends', GetAnalyticsTrendsController::class)->name('trends');
    Route::get('forecast', GetAnalyticsForecastController::class)->name('forecast');
    Route::post('recalculate', RecalculateAnalyticsController::class)->name('recalculate');
});

// Bugs Resource Routes
Route::prefix('bugs')->name('bugs.')->group(function () {
    Route::get('/', ListBugsController::class)->name('index');
    Route::post('/', StoreBugController::class)->name('store');
    Route::get('{bug}', ShowBugController::class)->name('show');
    Route::put('{bug}', UpdateBugController::class)->name('update');
    Route::delete('{bug}', DeleteBugController::class)->name('destroy');
});
