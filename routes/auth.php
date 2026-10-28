<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Dashboard\ClientsController;
use App\Http\Controllers\Dashboard\CompanyController;
use App\Http\Controllers\Landing\LoginController  as LandingLoginController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\OverviewController;
use App\Http\Controllers\Dashboard\PostingController;
use App\Http\Controllers\Dashboard\ProfileController as DashboardProfileController;
use App\Http\Controllers\Dashboard\StaffController;
use App\Http\Controllers\Dashboard\SubscriptionController;
use App\Http\Controllers\Dashboard\SystemController;
use App\Http\Controllers\Dashboard\LoginController as DashboardLoginController;
use App\Http\Controllers\Dashboard\ForgotPasswordController as DashboardForgotPasswordController;
use App\Http\Controllers\Landing\SignupController;
use Illuminate\Support\Facades\Route;

// Route::middleware('guest')->name('dashboard.')->prefix('dashboard')->group(function () {
//     Route::get('login',  [DashboardLoginController::class, 'create'])->name('login');
//     Route::post('login', [DashboardLoginController::class, 'store'])->name('store');
// });
Route::name('dashboard.')->prefix('dashboard')->group(function () {
    Route::middleware('admin.guest')->group(function () {
        Route::get('/login',                 [DashboardLoginController::class, 'index'])->name('login');
        Route::post('/login',                [DashboardLoginController::class, 'store'])->name('login.store');

        Route::get('/forgot-password',         [DashboardForgotPasswordController::class, 'create'])->name('password.request');
        Route::post('/forgot-password',        [DashboardForgotPasswordController::class, 'store'])->name('password.email');
        Route::get('/reset-password/{token}',  [DashboardForgotPasswordController::class, 'edit'])->name('password.reset');
        Route::post('/reset-password',         [DashboardForgotPasswordController::class, 'update'])->name('password.store');
    });
    Route::middleware('admin.auth')->group(function () {
        Route::post('/logout',      [DashboardLoginController::class, 'destroy'])->name('logout');
    });
    // Route::get('/profile',      [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile',    [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile',   [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::name('landing.')->group(function () {
    Route::middleware('landing.guest')->group(function () {
        Route::get('/verification/{token}', [SignupController::class, 'create'])->name('verification');
        Route::get('/login',                [LandingLoginController::class, 'index'])->name('login.view');
        Route::post('/login',               [LandingLoginController::class, 'store'])->name('login.store');
        Route::post('/signup',              [SignupController::class, 'store'])->name('signup');
    });
    Route::middleware('landing.auth')->group(function () {
        Route::post('/logout',      [LandingLoginController::class, 'destroy'])->name('logout');
        // Route::get('/',      [DashboardLoginController::class, 'destroy'])->name('logout');
    });
    // Route::get('/profile',      [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile',    [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile',   [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// Route::middleware('guest')->group(function () {
//     // Route::get('register', [RegisteredUserController::class, 'create'])
//     //     ->name('register');

//     // Route::post('register', [RegisteredUserController::class, 'store']);

//     Route::get('login', [AuthenticatedSessionController::class, 'create'])
//         ->name('login');

//     Route::post('login', [AuthenticatedSessionController::class, 'store']);

//     Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
//         ->name('password.request');

//     Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
//         ->name('password.email');

//     Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
//         ->name('password.reset');

//     Route::post('reset-password', [NewPasswordController::class, 'store'])
//         ->name('password.store');
// });

// Route::middleware('auth')->group(function () {
//     Route::get('verify-email', EmailVerificationPromptController::class)
//         ->name('verification.notice');

//     Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
//         ->middleware(['signed', 'throttle:6,1'])
//         ->name('verification.verify');

//     Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
//         ->middleware('throttle:6,1')
//         ->name('verification.send');

//     Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
//         ->name('password.confirm');

//     Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

//     Route::put('password', [PasswordController::class, 'update'])->name('password.update');

//     Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
//         ->name('logout');
// });
