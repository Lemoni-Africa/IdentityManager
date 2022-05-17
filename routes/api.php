<?php

use App\Http\Controllers\LiveCoinController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\ValidateBvnController;
use App\Http\Controllers\ValidateCardsController;
use App\Http\Controllers\ValidateDriversLicenseController;
use App\Http\Controllers\ValidateNationalPassportController;
use App\Http\Controllers\ValidateNinController;
use App\Http\Controllers\ValidatePhoneNumberController;
use App\Http\Controllers\ValidateSelfieController;
use App\Http\Controllers\ValidateVotersCardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/





switch (env('provider')) {
    case 'identityPass':
        Route::post('/validateBvn', [ValidateBvnController::class, 'store']);
        Route::post('/validatePhoneNumber', [ValidatePhoneNumberController::class, 'store']);
        Route::post('/validatePhoneNumberAndLastName', [ValidatePhoneNumberController::class, 'number']);
        Route::post('/validateDriversLicense', [ValidateDriversLicenseController::class, 'store']);
        Route::post('/validateVotersCard', [ValidateVotersCardController::class, 'store']);
        Route::post('/validateNin', [ValidateNinController::class, 'store']);
        Route::post('/validateNationalPassport', [ValidateNationalPassportController::class, 'store']);
        Route::post('/validateCards', [ValidateCardsController::class, 'store']);
        break;
    case 'Dojah':
        Route::post('/validateBvn', [ValidateBvnController::class, 'storeDojah']);
        Route::post('/validatePhoneNumber', [ValidatePhoneNumberController::class, 'DojaStore']);
        Route::post('/validatePhoneNumberAndLastName', [ValidatePhoneNumberController::class, 'DojaNumber']);
        // Route::post('/validateDriversLicense', [ValidateDriversLicenseController::class, 'DojahStore']);
        // Route::post('/validateVotersCard', [ValidateVotersCardController::class, 'DojahStore']);
        // Route::post('/validateNin', [ValidateNinController::class, 'store']);
        // Route::post('/validateNationalPassport', [ValidateNationalPassportController::class, 'store']);
        Route::post('/validateCards', [ValidateCardsController::class, 'DojahStore']);
        break;

    default:
        # code...
        break;
}

Route::post('/validateBvnSelfie', [ValidateSelfieController::class, 'BvnSelfie']);
Route::post('/validateNinSelfie', [ValidateSelfieController::class, 'NinSelfie']);
Route::get('/livecoin', [LiveCoinController::class, 'getCoin']);

Route::group(array('prefix' => 'sms' ), function () {
    Route::post('create', [SmsController::class, 'createSms']);
});



// Route::post('/validateBvn', [ValidateBvnController::class, 'store']);
// Route::post('/validatePhoneNumber', [ValidatePhoneNumberController::class, 'store']);
// Route::post('/validatePhoneNumberAndLastName', [ValidatePhoneNumberController::class, 'number']);
// Route::post('/validateDriversLicense', [ValidateDriversLicenseController::class, 'store']);
// Route::post('/validateVotersCard', [ValidateVotersCardController::class, 'store']);
// Route::post('/validateNin', [ValidateNinController::class, 'store']);
// Route::post('/validateNationalPassport', [ValidateNationalPassportController::class, 'store']);

//
// Route::post('/validateCards', [ValidateCardsController::class, 'store']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
