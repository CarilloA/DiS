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
Route::post('/select-role', [App\Http\Controllers\Auth\LoginController::class, 'selectRole'])->name('select-role');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index']);

//Apply the change deafault password middleware to relevant routes
use App\Http\Controllers\DashboardController;
Route::middleware(['auth', 'check.default_password'])->group(function () {
    Route::resource('dashboard', DashboardController::class);
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
});


// use App\Http\Controllers\DashboardController;
// Route::resource('dashboard', DashboardController::class);
// Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
// // // Route::delete('/dashboard/{id}', [App\Http\Controllers\DashboardController::class, 'destroy']);

use App\Http\Controllers\RegisterAccountController;
Route::resource('register_account', RegisterAccountController::class);
Route::get('create', [RegisterAccountController::class, 'create'])->name('createAccount');
Route::get('/admin/register', [RegisterAccountController::class, 'showRegistrationForm'])->name('admin.register');
Route::post('/admin/register', [RegisterAccountController::class, 'adminRegister'])->name('admin.register.submit');

use App\Http\Controllers\AccountManagementController;
Route::resource('account_management', AccountManagementController::class);
Route::get('confirm-email/{id}', [AccountManagementController::class, 'confirmEmail'])->name('confirm.email');
Route::post('/resend-confirmation/{id}', [AccountManagementController::class, 'resendConfirmationEmail'])->name('resend_confirmation_email');
Route::post('confirm_account/{id}', [AccountManagementController::class, 'confirmAccount'])->name('confirm_account');
Route::post('reject_account/{id}', [AccountManagementController::class, 'rejectAccount'])->name('reject_account');
Route::get('accounts_table', [AccountManagementController::class, 'index'])->name('accounts_table');
Route::get('create', [AccountManagementController::class, 'create'])->name('create_account');
Route::delete('delete/{id}', [AccountManagementController::class, 'destroy'])->name('delete_account');

//filter accounts
Route::get('/account-management/confirm_reject_filter', [AccountManagementController::class, 'confirmRejectFilter'])->name('accounts_table.confirm_reject_filter');
Route::get('/account-management/resend_link_filter', [AccountManagementController::class, 'resendLinkFilter'])->name('accounts_table.resend_link_filter');


// for change default password
Route::get('/change-password', [AccountManagementController::class, 'changePassword'])->name('password.change');
Route::post('/change-password', [AccountManagementController::class, 'updatePassword'])->name('password_update');


use App\Http\Controllers\ProfileController;
Route::resource('profile', ProfileController::class);
Route::put('/profile/update/{field}', [ProfileController::class, 'update'])->name('profile.update'); //new

Route::get('show_profile', [ProfileController::class, 'show'])->name('show_profile');
Route::get('edit_profile/{id}', [ProfileController::class, 'edit'])->name('edit_profile');
// Route::put('update_profile/{id}', [ProfileController::class, 'update'])->name('update_profile');

use App\Http\Controllers\InventoryController;
Route::resource('inventory', InventoryController::class);
Route::get('inventory_table', [InventoryController::class, 'index'])->name('inventory_table');
// Route::delete('delete/{id}', [InventoryController::class, 'destroy'])->name('delete_product');

//filter inventory
Route::get('product_name_filter', [InventoryController::class, 'productNameFilter'])->name('product_name_filter');
Route::get('category_filter', [InventoryController::class, 'CategoryFilter'])->name('category_filter');
Route::get('supplier_filter', [InventoryController::class, 'supplierFilter'])->name('supplier_filter');

use App\Http\Controllers\PurchaseController;
Route::resource('purchase', PurchaseController::class);
Route::get('purchase_table', [PurchaseController::class, 'index'])->name('purchase_table');
Route::post('details', [PurchaseController::class, 'getSupplierDetails']);
Route::get('create', [PurchaseController::class, 'create'])->name('create_product');
Route::post('restock', [PurchaseController::class, 'restock'])->name('restock_product');
Route::post('restock_store_product', [PurchaseController::class, 'restockStoreProduct'])->name('restock_store_product');
Route::delete('delete/{id}', [PurchaseController::class, 'destroy'])->name('delete_product');

//filter products table
Route::get('filter_product_name', [PurchaseController::class, 'productNameFilter'])->name('filter_product_name');
Route::get('filter_category', [PurchaseController::class, 'CategoryFilter'])->name('filter_category');
Route::get('filter_supplier', [PurchaseController::class, 'supplierFilter'])->name('filter_supplier');
Route::get('filter_store_restock', [PurchaseController::class, 'storeRestockFilter'])->name('filter_store_restock');
Route::get('filter_stockroom_restock', [PurchaseController::class, 'stockroomRestockFilter'])->name('filter_stockroom_restock');


use App\Http\Controllers\SalesController;
Route::resource('sales', SalesController::class);
Route::get('sales_table', [SalesController::class, 'index'])->name('sales_table');
Route::get('create', [SalesController::class, 'create'])->name('sale_product');
Route::post('fetch-product', [SalesController::class, 'fetchProduct'])->name('fetch.product');
Route::get('search', [SalesController::class, 'search'])->name('sales.search');

use App\Http\Controllers\ReturnProductController;
Route::get('return_product', [ReturnProductController::class, 'index'])->name('return_product_table');
Route::get('return_product/{id}', [ReturnProductController::class, 'showReturnForm'])->name('return_product.show');
Route::post('return_product/{id}', [ReturnProductController::class, 'processReturn'])->name('return_product.process');

use App\Http\Controllers\InventoryAuditController;
Route::resource('inventory_audit', InventoryAuditController::class);
Route::get('audit_inventory_table', [InventoryAuditController::class, 'index'])->name('audit_inventory_table');
Route::post('update/{inventory_id}', [InventoryAuditController::class, 'update'])->name('inventory.audit.update');
Route::get('logs', [InventoryAuditController::class, 'logs'])->name('inventory.audit.logs');
Route::get('/step1', [InventoryAuditController::class, 'showStep1'])->name('step1');
Route::post('/inventory-audit/step2', [InventoryAuditController::class, 'submitStep1AndGoToStep2'])->name('inventory.audit.step2');
Route::get('/step2', [InventoryAuditController::class, 'showStep2'])->name('step2');
Route::post('/inventory-audit/step3', [InventoryAuditController::class, 'submitStep2AndGoToStep3'])->name('inventory.audit.step3');
Route::get('/step3', [InventoryAuditController::class, 'showStep3'])->name('step3');
Route::post('/inventory-audit/step4', [InventoryAuditController::class, 'submitStep3AndGoToStep4'])->name('inventory.audit.step4');
Route::get('/step4', [InventoryAuditController::class, 'showStep4'])->name('step4');
Route::post('/submit/step4', [InventoryAuditController::class, 'submitStep4'])->name('submit.step4');

use App\Http\Controllers\ReportController;
Route::post('inventory_report', [ReportController::class, 'generateReport'])->name('report.generate');
Route::post('generate_filter_report', [ReportController::class, 'generateFilteredReport'])->name('generate_filter_report');
Route::post('audit_inventory_report', [ReportController::class, 'generateAuditReport'])->name('audit.report.generate');

use App\Http\Controllers\ScrapController;
Route::post('dispose', [ScrapController::class, 'disposeProduct'])->name('dispose_product');





