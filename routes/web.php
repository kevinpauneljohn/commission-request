<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

require __DIR__ . '/web/user.php';
require __DIR__ . '/web/rolesAndPermissions.php';
require __DIR__ . '/web/requests.php';
require __DIR__ . '/web/task.php';
require __DIR__ . '/web/actionTaken.php';
require __DIR__ . '/web/finding.php';
require __DIR__ . '/web/automation.php';
require __DIR__ . '/web/automationTask.php';

Route::get('/', function () {
    return redirect(\route('home'));
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/multi-tasking', [App\Http\Controllers\HomeController::class, 'multiTasking'])->name('multi-tasking');
