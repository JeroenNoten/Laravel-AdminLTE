<?php

use Illuminate\Support\Facades\Route;
use JeroenNoten\LaravelAdminLte\Http\Controllers\DarkModeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your package. These
| routes are loaded by your package ServiceProvider within a group which
| contains the "web" middleware group.
|
*/

//-----------------------------------------------------------------------------
// Dark Mode routes.
//-----------------------------------------------------------------------------

Route::post('/darkmode/toggle', [DarkModeController::class, 'toggle'])
    ->name('darkmode.toggle');
