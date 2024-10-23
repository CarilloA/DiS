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

// Forgot Password Routes
Route::get('password/reset', 'App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');

// Reset Password Routes
Route::get('password/reset/{token}', 'App\Http\Controllers\Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'App\Http\Controllers\Auth\ResetPasswordController@reset')->name('password.update');


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

use App\Http\Controllers\InventoryController;
Route::resource('inventory', InventoryController::class);
Route::get('inventory_table', [InventoryController::class, 'index'])->name('inventory_table');
Route::get('show/{id}', [InventoryController::class, 'show'])->name('show_product');
Route::get('create', [InventoryController::class, 'create'])->name('create_product');
Route::get('edit_product/{id}', [InventoryController::class, 'edit'])->name('edit_product');
Route::put('update_product/{id}', [InventoryController::class, 'update'])->name('update_product');
Route::delete('delete/{id}', [InventoryController::class, 'destroy'])->name('delete_product');

use App\Http\Controllers\StockTransferController;
Route::get('/stock_transfers', [StockTransferController::class, 'index'])->name('stock_transfers');
Route::get('/stock_transfer/create', [StockTransferController::class, 'create'])->name('create_transfer');
Route::post('/stock_transfer/store', [StockTransferController::class, 'store'])->name('stock_transfer.store');

use App\Http\Controllers\PurchaseController;
Route::resource('purchase', PurchaseController::class);
Route::get('purchase_table', [PurchaseController::class, 'index'])->name('purchase_table');
Route::get('create', [PurchaseController::class, 'create'])->name('create_product');
Route::post('restock', [PurchaseController::class, 'restock'])->name('restock_product');

use App\Http\Controllers\SalesController;
Route::resource('sales', SalesController::class);
Route::get('sales_table', [SalesController::class, 'index'])->name('sales_table');
Route::get('create', [SalesController::class, 'create'])->name('sale_product');
Route::post('fetch-product', [SalesController::class, 'fetchProduct'])->name('fetch.product');
Route::get('search', [SalesController::class, 'search'])->name('sales.search');

use App\Http\Controllers\ReturnProductController;
Route::get('return_product', [ReturnProductController::class, 'index'])->name('return_product.index');
Route::get('return_product/{id}', [ReturnProductController::class, 'showReturnForm'])->name('return_product.show');
Route::post('return_product/{id}', [ReturnProductController::class, 'processReturn'])->name('return_product.process');

Route::get('refund_exchange', [ReturnProductController::class, 'showRefundExchangeForm']);
Route::post('refund_exchange', [ReturnProductController::class, 'processRefundOrExchange']);



