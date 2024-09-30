<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

// Redirect root ('/') to the login page if the user is not authenticated.
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard'); // If the user is authenticated, redirect to the home page.
    }
    return redirect('/login'); // If the user is not authenticated, redirect to the login page.
});


Auth::routes();
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index']);


use App\Http\Controllers\DashboardController;
Route::resource('dashboard', DashboardController::class);
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index']);
Route::delete('/dashboard/{id}', [App\Http\Controllers\DashboardController::class, 'destroy']);

use App\Http\Controllers\AccountManagementController;
Route::resource('account_management', AccountManagementController::class);
Route::get('confirm-email/{id}', [AccountManagementController::class, 'confirmEmail'])->name('confirm.email');
Route::get('accounts_table', [AccountManagementController::class, 'index'])->name('accounts_table');
// Route::get('show/{id}', [AccountManagementController::class, 'show'])->name('show_account');
Route::get('create', [AccountManagementController::class, 'create'])->name('create_account');
// Route::get('edit/{id}', [AccountManagementController::class, 'edit'])->name('edit_account');
// Route::put('update/{id}', [AccountManagementController::class, 'update'])->name('update_account');
Route::delete('delete/{id}', [AccountManagementController::class, 'destroy'])->name('delete_account');

use App\Http\Controllers\ProfileController;
Route::resource('profile', ProfileController::class);
Route::get('show_profile', [ProfileController::class, 'show'])->name('show_profile');
Route::get('edit_profile/{id}', [ProfileController::class, 'edit'])->name('edit_profile');
Route::put('update_profile/{id}', [ProfileController::class, 'update'])->name('update_profile');