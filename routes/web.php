<?php

use App\Http\Controllers\CustomController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('administrator');
});

Route::get('form-details/{cancerDiagnose}', [CustomController::class, 'showForm'])
    ->middleware(['auth', 'can:view,cancerDiagnose'])
    ->name('form-details');

Route::get('follow-up-form-details/{patientFollowUpForm}', [CustomController::class, 'showFollowUpForm'])
    ->middleware(['auth', 'can:view,patientFollowUpForm'])
    ->name('follow-up-form-details');
