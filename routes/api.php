<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\CallAppointmentController;
use App\Http\Controllers\API\AvailableTimeController;
use App\Http\Controllers\API\SupportController;
use App\Http\Controllers\API\NotificationPreferenceController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\AstrologyController;


use App\Http\Controllers\PageController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/create-new-password', [AuthController::class, 'createNewPassword']);

    // this api hit after login
    Route::middleware('jwt.auth')->group(function () {

    Route::get('/get-profile/{user_id}', [AuthController::class, 'getProfile']);
    Route::post('/edit-profile', [AuthController::class, 'editProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken']);
        Route::post('/edit-profile', [AuthController::class, 'editProfile']);
    Route::post('/delete-account', [AuthController::class, 'deleteAccount']);

    Route::post('/book-call', [CallAppointmentController::class, 'bookCall']);
    Route::post('/booked-calls', [CallAppointmentController::class, 'listBookedCallsByUser']);
    Route::get('/booked-call-details/{booking_dd}', [CallAppointmentController::class, 'bookedCallDetails']);
    Route::put('/update-appointment-status/{schedule_calls_id}', [CallAppointmentController::class, 'updateStatus']);
    Route::post('/reschedule-call', [CallAppointmentController::class, 'rescheduleCall']);
    Route::post('/cancel-call', [CallAppointmentController::class, 'cancelCall']);
    Route::get('/generate-call-receipt/{schedule_calls_id}', [CallAppointmentController::class, 'generateReceipt']);


    Route::prefix('dashboard')->group(function () {

        Route::post('/', [DashboardController::class, 'index']);
    });

    Route::prefix('kundali')->group(function () {

        Route::post('/', [AstrologyController::class, 'getKundaliData']);
    });

    Route::prefix('remedies')->group(function () {

        Route::post('/', [AstrologyController::class, 'getRemediesData']);
    });

    Route::prefix('planet')->group(function () {

        Route::post('/', [AstrologyController::class, 'getPlanetData']);
        Route::post('/details', [AstrologyController::class, 'getPlanetDetails']);
    });

    Route::prefix('slot')->group(function () {

        Route::get('/list/{date?}', [AvailableTimeController::class, 'index']);
    });

    Route::prefix('transactions')->group(function () {

        // Route::post('/initiate', [TransactionController::class, 'initiateTransaction']);
        Route::put('/{transaction_id}/update', [TransactionController::class, 'updateTransaction']);
        Route::get('/{transaction_id}', [TransactionController::class, 'getTransaction']);
    });

    Route::prefix('supports')->group(function () {

        Route::post('/', [SupportController::class, 'store']);
        Route::get('/{email}', [SupportController::class, 'show']);
    });

    Route::prefix('notification')->group(function () {

        Route::get('/preferences/{user_id}', [NotificationPreferenceController::class, 'getPreferences']);
        Route::put('/preferences/update', [NotificationPreferenceController::class, 'updatePreferences']);
        Route::post('/list', [NotificationPreferenceController::class, 'userNotificationList']);
        Route::post('/mark-as-read', [NotificationPreferenceController::class, 'userNotificationMarkAsRead']);
        Route::post('/mark-all-as-read', [NotificationPreferenceController::class, 'userNotificationMarkAllAsRead']);
    });
});
});

Route::get('/privacy-policy', [PageController::class, 'privacyPolicy']);
Route::get('/term-condition', [PageController::class, 'termCondition']);
Route::get('/faq', [PageController::class, 'faq']);
