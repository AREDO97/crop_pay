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
use App\Http\Controllers\api\AdminDashboardController;
use App\Http\Controllers\api\ProfileController;
use Illuminate\Http\Request;
use App\Http\Controllers\auth\EmailVerificationController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\PasswordResetController;

// register & login
Route::prefix('v1')->middleware(['throttle:10,1'])->group(function (){
// register user
Route::post('register',[AuthController::class,'register']);
// login user
Route::post('login',[authController::class,'login']);
});

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
Route::prefix('v1')->middleware(['auth:sanctum', 'role:admin,super_admin'])->group(function () {
    // all users
    Route::get('users',[UserController::class,'index'])->name('view users');
    // user activity
    Route::get('userActivity',[UserController::class,'userActivity'])->name('user activity');
    // access a single user
    Route::get('users/{user}',[UserController::class,'oneUser'])->name('view single user');
    // update user info
    Route::patch('users/update/{user}',[UserController::class,'update'])->name('update user info');
    // suspend user
    Route::patch('users/suspend/{user}', [UserController::class, 'softDelete'])->name('suspend user');
    // create an admin
    Route::patch('users/create_admin/{user}', [UserController::class, 'makeAdmin'])->name('make admin');
    // demote admin to user
    Route::patch('users/demote_admin/{user}', [UserController::class, 'demoteAdmin'])->name('demote admin');
    // unsuspend user
     Route::patch('users/unsuspend/{user}', [UserController::class, 'unsuspend'])->name('unsuspend user');
    
});


// user profile management
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // show profile
    Route::get('user_profile',[ProfileController::class,'index']);
    // update profile
    Route::patch('user_profile',[ProfileController::class,'update']);

});

// saving category management
Route::prefix('v1')->middleware(['auth:sanctum','role:admin,super_admin'])->group(function (){
    // create saving category
    Route::post('saving_category',[SavingCategoryController::class,'create'])
    ->name('create category');
    // update saving category
    Route::patch('saving_category/{category}',[SavingCategoryController::class,'update'])
    ->name('update category');
    // delete saving category
    Route::delete('saving_category/{category}',[SavingCategoryController::class,'destroy'])
    ->name('delete category');
});
    // view all saving categories 
    Route::get('v1/saving_category',[SavingCategoryController::class,'index'])
 ->middleware('auth:sanctum')->name('view category');

 
 // user saving goal management
 Route::prefix('v1')->middleware(['auth:sanctum'])->group(function (){
    // User Saving Goals
    Route::get('saving_goals',[SavingGoalController::class,'UserSavingGoals']);
    // get all saving goals
    Route::get('saving_goals/admin',[SavingGoalController::class,'index']);
    // create saving goal
    Route::post('saving_goals',[SavingGoalController::class,'create']);
   // update saving goal
    Route::patch('saving_goals/{goal}',[SavingGoalController::class,'update']);
    // delete saving goal
    Route::delete('saving_goals/{goal}',[SavingGoalController::class,'destroy']);

 });

 // deposit management
 Route::prefix('v1')->middleware(['auth:sanctum'])->group(function (){
    // user deposits
    Route::get('deposits',[DepositController::class,'index']);
    // deposit money
    Route::post('deposits/{goal}',[DepositController::class,'create']);
 });
// all deposits
    Route::get('v1/deposits/admin',[DepositController::class,'deposits'])
    ->middleware(['auth:sanctum','role:admin,super_admin']);
 // pesa pal management
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function (){

Route::get('payments/pesapal/ipn', [ PesapalController::class, 'ipn'])
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
Route::prefix('v1')->middleware(['auth:sanctum','role:admin,super_admin'])
->group(function(){
    // create product
    Route::post('products',[ProductController::class,'create'])
    ->name('create product');
    // update product
    Route::patch('products/{product}',[ProductController::class,'update'])
    ->name('update product');
    // delete product
    Route::delete('products/{product}',[ProductController::class,'destroy'])
    ->name('delete product');
});

    // product sale management
Route::prefix('v1')->middleware(['auth:sanctum','role:admin,super_admin'])->group(function(){
  
    // user product sales
  Route::get('product_sales',[ProductSaleController::class,'index']);
    // update sale status
  Route::patch('product_sales/{sale}',[ProductSaleController::class,'resolveSale']);
    // all platform product sales
  Route::get('product_sales/admin',[ProductSaleController::class,'allProductSales']);
});

   // create product sale
  Route::post('v1/product_sales',[ProductSaleController::class,'create'])
  ->middleware('auth:sanctum','role:admin,super_admin');

    // notifications management
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // all user notifications
  Route::get('notifications', [NotificationsController::class,'index'])
  ->name('all notifications');
   // mark notifications as read
  Route::post('notifications/markAsRead', [ NotificationsController::class, 'markAllAsRead'])
  ->name('marks as read');
   // delete notifications
  Route::delete('notifications/{id}',[NotificationsController::class,'destroy'])
  ->name('delete notification');
});


    // settings management
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // updatePassword
Route::patch('password',[SettingsController::class,'updatePassword'])
->name('update password');
    // update username and email  updateUserInfo
Route::patch('usernameOrEmail',[SettingsController::class,'updateUserInfo'])
->name('update name or email');
    // delete account
Route::delete('deleteAccount',[SettingsController::class,'deleteAccount'])
->name('delete account');

});

// audit log management
Route::prefix('v1')->middleware(['auth:sanctum','role:super_admin,admin'])->group(function (){
    // view logs
    Route::get('logs',[AuditLogController::class,'index']);
    // delete log
    Route::delete('logs/{log}',[AuditLogController::class,'destroy']);
    // delete old logs
    Route::delete('logs',[AuditLogController::class,'clearAll']);
    // archive log
    Route::patch('logs/{log}/archive',[AuditLogController::class,'archive']);
});

// health checks
Route::prefix('v1')->middleware(['auth:sanctum','role:admin,super_admin','verified','throttle:10,1'])
->group(function () {
    // Check Laravel + MySQL 
    Route::get('health', [HealthController::class, 'health']); 
    // Checks if api is alive 
    Route::get('health/live', [HealthController::class, 'live']);
});


// dashboard stats
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Admin stats endpoint
    Route::get('/admin/stats', [AdminDashboardController::class, 'stats']);
});