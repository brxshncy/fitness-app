<?php

use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/admin/login', [AuthenticationController::class, "loginAsAdmin"])->name('admin.login');
Route::get('/admin/logout', [AuthenticationController::class, 'logout'])->name('admin.logout');


Route::prefix('admin')
    ->middleware(['auth:sanctum', 'role:ADMIN'])
    ->as('admin.')
    ->group(function () {
        Route::apiResource('organization', OrganizationController::class);
});