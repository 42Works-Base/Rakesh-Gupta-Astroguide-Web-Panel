<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AvailableTimeController;
use App\Http\Controllers\CallAppointmentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DebugCallReminderController;

use App\Http\Controllers\AdminSeederController;
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

Route::get('/clear-cache', function () {
    $exitCode = \Artisan::call('cache:clear');
    $exitCode = \Artisan::call('config:cache');
    $exitCode = \Artisan::call('config:clear');
    $exitCode = \Artisan::call('view:clear');
    $exitCode = \Artisan::call('route:clear');
    $exitCode = \Artisan::call('optimize:clear');
    echo "cache clear succesfully.";
    // return what you want
});

Route::get('/seed-test-admins', [AdminSeederController::class, 'seedTestAdmins']);

Route::get('/test-call-reminders', [DebugCallReminderController::class, 'testReminders']);
Route::get('/test-mark-old-calls-as-completed', [DebugCallReminderController::class, 'testMarkOldCallsAsCompleted']);

Route::get('/whatsapp/{bookingId}', [CallAppointmentController::class, 'redirectToWhatsApp'])->name('whatsapp.redirect');

Route::get('/', [AuthController::class, 'index'])->name('home');
Route::get('/login', [AuthController::class, 'index'])->name('home');

Route::post('/login', [AuthController::class, 'authenticate'])->name('login');
// Forgot Password Form
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');

// Handle Password Reset Link Request
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

// Reset Password Form
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');

// Handle Reset Password
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['auth']], function () {
    // Change Password
    // Show Change Password Form
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.change.form');

    // Save New Password
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.change');


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/transactions/chart-data', [DashboardController::class, 'getChartData'])->name('transactions.chart-data');

    // Show form
    Route::get('/general-remedies/update', [DashboardController::class, 'showGeneralRemediesForm'])->name('general-remedies.update');

    // Handle form submission
    Route::post('/general-remedies/update', [DashboardController::class, 'updateGeneralRemedies'])->name('general-remedies.save');


    Route::prefix('admin')->name('admin.')->group(function () {
        // Profile Routes
        Route::get('/profile', [UserController::class, 'adminProfile'])->name('profile');
        Route::put('/profile/update', [UserController::class, 'adminProfileUpdate'])->name('profile.update');

        // Password Routes
        Route::get('/password/update', [UserController::class, 'adminPassword'])->name('password');
        Route::put('/password/update', [UserController::class, 'adminPasswordUpdate'])->name('password.update');

        // Bank Routes
        Route::get('/bank', [UserController::class, 'adminBank'])->name('bank');
        Route::put('/bank/update', [UserController::class, 'adminBankUpdate'])->name('bank.update');
    });


    //employee Route start
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        // Route::get('/create', [UserController::class, 'showForm'])->name('users.create');
        // Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{id}/edit', [UserController::class, 'showForm'])->name('users.edit');
        // Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
        // Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('details/{id?}', [UserController::class, 'userDetails'])->name('user.details');
        Route::post('/block-unblock/{id}', [UserController::class, 'blockUnblock'])->name('users.block-unblock');
    });
    //employee Route end

    Route::group(['prefix' => 'appointment-management'], function () {
        Route::get('/', [CallAppointmentController::class, 'index'])->name('appointment-management.index');
        Route::get('/{id}/details', [CallAppointmentController::class, 'details'])->name('appointment-management.details');
        Route::get('/update-appointment-status/{id}', [CallAppointmentController::class, 'updateStatus']);
        Route::post('/mark-call-cancel/{id}', [CallAppointmentController::class, 'markCallCancel']);
        Route::post('/refund/{id}', [CallAppointmentController::class, 'refund']);
        Route::get('/{schedule_call_id}/user-chart', [CallAppointmentController::class, 'userChart'])->name('appointment-management.user.chart');
        Route::get('/{schedule_call_id}/user-predictions', [CallAppointmentController::class, 'userPredictions'])->name('appointment-management.user.predictions');
        Route::post('/{schedule_call_id}/user-predictions', [CallAppointmentController::class, 'handlePredictionType'])->name('appointment-management.user.predictions.submit');
    });

    Route::group(['prefix' => 'slot-management'], function () {
        Route::get('/', [AvailableTimeController::class, 'index'])->name('slot-management.index');
        Route::get('/create', [AvailableTimeController::class, 'showCreateForm'])->name('slot-management.create');
        Route::post('/store', [AvailableTimeController::class, 'store'])->name('slot-management.store');
        Route::post('/availability/{id}', [AvailableTimeController::class, 'UpdateAvailability'])->name('slot-management.availability');
        Route::delete('/{id}', [AvailableTimeController::class, 'destroy'])->name('slot-management.destroy');
    });

    Route::group(['prefix' => 'transactions'], function () {
        Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');
    });

    Route::group(['prefix' => 'notification'], function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notification.index');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notification.markAllRead');
        Route::post('/mark-read', [NotificationController::class, 'markRead'])->name('notification.markRead');
    });


});
