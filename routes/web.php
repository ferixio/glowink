<?php

use Illuminate\Support\Facades\Route;
use App\Console\Commands\MigrateOldToUsers;

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

Route::get('/', function () {
    return redirect('/user');
});
Route::get('cek', [MigrateOldToUsers::class, 'handle']);
