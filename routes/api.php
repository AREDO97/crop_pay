<?php

use App\Http\Controllers\api\AuditLogController;
use App\Http\Controllers\api\DepositController;
use App\Http\Controllers\api\NotificationsController;
use App\Http\Controllers\api\SavingCategoryController;
use App\Http\Controllers\api\SavingGoalController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\auth\AuthController;
 use App\Http\Controllers\api\PesapalController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\ProductSaleController;
use App\Http\Controllers\api\SettingsController;
use App\Http\Controllers\api\HealthController;
use Illuminate\Http\Request;
use App\Http\Controllers\auth\EmailVerificationController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\PasswordResetController;

// register user
Route::post('v1/register',[AuthController::class,'register'])->middleware('throttle:5,1')
->name('register');
// login user
Route::post('v1/login',[authController::class,'login'])->middleware('throttle:5,1')
->name('login');
// log out endpoint
Route::post('v1/logout',[authController::class,'logout'])->middleware('auth:sanctum')
->name('logout');

// google oauth
Route::get('v1/oauth/google', [AuthController::class, 'google'])
->name('google redirect');
Route::get('v1/oauth/google/callback', [AuthController::class, 'googleCallback'])
->name('google callback');

//  password reset management
Route::post('/v1/forgot-password', [ PasswordResetController::class, 'forgotPassword']);

Route::post('/v1/reset-password', [PasswordResetController::class,'resetPassword']);

// email verification management
Route::get('v1/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify']
)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

Route::post('v1/email/verification-notification', [EmailVerificationController::class, 'resend']
)->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

// current looged in user
Route::get('v1/getMe',[AuthController::class,'getMe'])->middleware('auth:sanctum')
->name('auth me');

// user management only admins $ super_admins
Route::middleware(['auth:sanctum', 'role:admin,super_admin','throttle:5,1'])->group(function () {
    // all users
    Route::get('v1/users',[UserController::class,'index'])->name('view users');
    // user activity
    Route::get('v1/userActivity',[UserController::class,'userActivity'])->name('user activity');
    // access a single user
    Route::get('v1/users/{user}',[UserController::class,'oneUser'])->name('view single user');
    // update user info
    Route::patch('v1/users/update/{user}',[UserController::class,'update'])->name('update user info');
    // suspend user
    Route::patch('v1/users/suspend/{user}', [UserController::class, 'softDelete'])->name('suspend user');
    // create an admin
    Route::patch('v1/users/create_admin/{user}', [UserController::class, 'makeAdmin'])->name('make admin');
    // demote admin to user
    Route::patch('v1/users/demote_admin/{user}', [UserController::class, 'demoteAdmin'])->name('demote admin');
    // unsuspend user
     Route::patch('v1/users/unsuspend/{user}', [UserController::class, 'unsuspend'])->name('unsuspend user');
    
});


// saving category management
Route::middleware(['auth:sanctum','role:admin,super_admin','throttle:5,1'])->group(function (){
    // create saving category
    Route::post('v1/saving_category',[SavingCategoryController::class,'create'])
    ->name('create category');
    // update saving category
    Route::patch('v1/saving_category/{category}',[SavingCategoryController::class,'update'])
    ->name('update category');
    // delete saving category
    Route::delete('v1/saving_category/{category}',[SavingCategoryController::class,'destroy'])
    ->name('delete category');
});
    // view all saving categories 
    Route::get('v1/saving_category',[SavingCategoryController::class,'index'])
 ->middleware('auth:sanctum')->name('view category');

 
 // user saving goal management
 Route::middleware(['auth:sanctum','throttle:5,1'])->group(function (){
    // User Saving Goals
    Route::get('v1/saving_goals',[SavingGoalController::class,'UserSavingGoals']);
    // create saving goal
    Route::post('v1/saving_goals',[SavingGoalController::class,'create']);
   // update saving goal
    Route::patch('v1/saving_goals/{goal}',[SavingGoalController::class,'update']);

 });

 // deposit management
 Route::middleware(['auth:sanctum','throttle:4,1'])->group(function (){
    // user deposits
    Route::get('/v1/deposits',[DepositController::class,'index']);
    // deposit money
    Route::post('/v1/deposits/{goal}',[DepositController::class,'create']);
 });
// all deposits
    Route::get('v1/deposits/admin',[DepositController::class,'deposits'])
    ->middleware(['auth:sanctum','role:admin,super_admin','throttle:5,1']);
 // pesa pal management
Route::middleware(['auth:sanctum','throttle:5,1'])->group(function (){

Route::get('v1/payments/pesapal/ipn', [ PesapalController::class, 'ipn'])
->name('pesapal.ipn');
});

Route::get('v1/payments/pesapal/callback', [PesapalController::class,'callback'])
->name('pesapal.callback');
// cancel payment
Route::get('v1/payments/pesapal/cancel', [ PesapalController::class,  'cancel'])
->name('pesapal.cancel');


// product management
// view all products
Route::get('v1/products',[ProductController::class,'index'])
->middleware('auth:sanctum')->name('view products');
// product admin management
Route::middleware(['auth:sanctum','role:admin,super_admin','throttle:5,1'])->group(function(){
    // create product
    Route::post('v1/products',[ProductController::class,'create'])
    ->name('create product');
    // update product
    Route::patch('v1/products/{product}',[ProductController::class,'update'])
    ->name('update product');
    // delete product
    Route::delete('v1/products/{product}',[ProductController::class,'destroy'])
    ->name('delete product');
});

    // product sale management
Route::middleware(['auth:sanctum','throttle:5,1'])->group(function(){
    // create product sale
  Route::post('v1/product_sales',[ProductSaleController::class,'create']);
    // user product sales
  Route::get('/v1/product_sales',[ProductSaleController::class,'index']);
    // all platform product sales
  Route::get('v1/admin/product_sales',[ProductSaleController::class,'allProductSales']);
});


    // notifications management
Route::middleware(['auth:sanctum'])->group(function () {
    // all user notifications
  Route::get('v1/notifications', [NotificationsController::class,'index'])
  ->name('all notifications');
   // mark notifications as read
  Route::post('v1/notifications/markAsRead', [ NotificationsController::class, 'markAllAsRead'])
  ->name('marks as read');
   // delete notifications
  Route::delete('v1/notifications/{id}',[NotificationsController::class,'destroy'])
  ->name('delete notification');
});


    // settings management
Route::middleware(['auth:sanctum','throttle:5,1'])->group(function () {
    // updatePassword
Route::patch('v1/password',[SettingsController::class,'updatePassword'])
->name('update password');
    // update username and email  updateUserInfo
Route::patch('v1/usernameOrEmail',[SettingsController::class,'updateUserInfo'])
->name('update name or email');
    // delete account
Route::delete('v1/deleteAccount',[SettingsController::class,'deleteAccount'])
->name('delete account');

});

// audit log management
Route::middleware(['auth:sanctum','role:super_admin,admin'])->group(function (){
    // view logs
    Route::get('v1/logs',[AuditLogController::class,'index']);
    // delete log
    Route::delete('v1/logs/{log}',[AuditLogController::class,'destroy']);
    // delete old logs
    Route::delete('v1/logs',[AuditLogController::class,'clearAll']);
    // archive log
    Route::patch('v1/logs/{log}/archive',[AuditLogController::class,'archive']);
});

// health checks
Route::middleware(['auth:sanctum','role:admin,super_admin','verified','throttle:5,1'])
->group(function () {
    // Check Laravel + MySQL 
    Route::get('v1/health', [HealthController::class, 'health']); 
    // Checks if api is alive 
    Route::get('v1/health/live', [HealthController::class, 'live']);
});


