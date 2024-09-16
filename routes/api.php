<?php

use App\Http\Controllers\Api\V1\Admin\Consumption\ConsumptionApiController;
use App\Http\Controllers\Api\V1\Admin\Customer\CustomerApiController;
use App\Http\Controllers\Api\V1\Admin\Meter\MeterApiController;
use App\Http\Controllers\Api\V1\Admin\MeterReading\MeterReadingApiController;
use App\Http\Controllers\Api\V1\Admin\MeterTariff\MeterTariffApiController;
use App\Http\Controllers\Api\V1\Admin\Permission\PermissionApiController;
use App\Http\Controllers\Api\V1\Admin\Role\RoleApiController;
use App\Http\Controllers\Api\V1\Admin\Tariff\TariffApiController;
use App\Http\Controllers\Api\V1\Admin\Tenant\TenantApiController;
use App\Http\Controllers\Api\V1\Admin\User\UserApiController;
use App\Http\Controllers\Api\V1\Admin\UtilityType\UtilityTypeApiController;
use App\Http\Controllers\Api\V1\Auth\AuthApiController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Customer\Customer\CustomerCustomerApiController;
use App\Http\Controllers\Api\V1\Customer\Meter\MeterCustomerApiController;
use App\Http\Controllers\Api\V1\Customer\Tenant\TenantCustomerApiController;
use App\Http\Controllers\Api\V1\User\Meter\MeterUserApiController;
use App\Http\Controllers\Api\V1\User\Shared\TimezoneController;
use App\Http\Controllers\Api\V1\User\Tenant\TenantUserApiController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'as' => 'api.'], function () {
    // Register
    Route::post('/register', [AuthApiController::class, 'register']);

    // Login
    Route::post('/login', [AuthApiController::class, 'login']);

    // Forgot Password
    Route::post('forgot-password', [ForgotPasswordController::class, 'forgotPassword']);

    // Reset Password
    Route::post('reset-password', [ForgotPasswordController::class, 'reset']);

    // Verify Email
    Route::get('verify/email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.show');
    Route::post('verify/email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');

    // Get Ping Route
    Route::get('/ping', function () {
        return response()->json(['message' => 'pong']);
    })->name('get-ping');

    // Post Ping Route
    Route::post('/ping', function () {
        return response()->json(['message' => 'pong']);
    })->name('post-ping');

    // Get Timezones
    Route::get('/timezones', [TimezoneController::class, 'getTimeZones']);
});

Route::group(['prefix' => 'v1', 'as' => 'api.', 'middleware' => ['auth:sanctum', 'check_status', 'set_user_timezone']], function () {
    // Logout
    Route::post('/logout', [AuthApiController::class, 'logout']);

    // Logout Other Sessions
    Route::post('/logout-other-sessions', [AuthApiController::class, 'logoutOtherSessions']);

    // Logout Session
    Route::post('/logout-session/{id}', [AuthApiController::class, 'logoutSession']);

    // Verify Email Resend
    Route::post('verify/email/resend', [EmailVerificationController::class, 'resend']);

    // Change Password
    Route::post('/change-password', [AuthApiController::class, 'changePassword']);

    // My Details
    Route::get('/my-details', [AuthApiController::class, 'myDetails']);
    Route::put('/my-details', [AuthApiController::class, 'updateMyDetails']);

    // My Tenants
    Route::get('/my-tenants', [TenantUserApiController::class, 'index']);

    // My Meters
    Route::get('/my-meters', [MeterUserApiController::class, 'index']);

    // My Meter Readings
    Route::get('/my-meters/{meter}/meter-readings', [MeterUserApiController::class, 'getMeterReadings']);

    // Customers
    Route::get('/customers', [CustomerCustomerApiController::class, 'index']);

    // Tenants
    Route::get('/tenants', [TenantCustomerApiController::class, 'index']);

    // Meters
    Route::get('/meters', [MeterCustomerApiController::class, 'index']);

    // Meter Readings
    Route::get('/meters/{meter}/meter-readings', [MeterCustomerApiController::class, 'getMeterReadings']);
});

Route::group(['prefix' => 'v1/admin', 'as' => 'api.admin.', 'middleware' => ['auth:sanctum', 'check_status', 'set_user_timezone']], function () {
    // Consumptions
    Route::apiResource('consumptions', ConsumptionApiController::class);

    // Customers
    Route::apiResource('customers', CustomerApiController::class);

    // Meters
    Route::apiResource('meters', MeterApiController::class);

    // Meter Readings
    Route::apiResource('meter-readings', MeterReadingApiController::class);

    // Meter Tariffs
    Route::apiResource('meter-tariffs', MeterTariffApiController::class);

    // Permissions
    Route::apiResource('permissions', PermissionApiController::class);

    // Roles
    Route::apiResource('roles', RoleApiController::class);

    // Tariffs
    Route::apiResource('tariffs', TariffApiController::class);

    // Tenants
    Route::apiResource('tenants', TenantApiController::class);

    // Users
    Route::apiResource('users', UserApiController::class);

    // Utility Types
    Route::apiResource('utility-types', UtilityTypeApiController::class);
});
