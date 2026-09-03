<?php

use App\Enums\Permission;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified.api'])
    ->controller(AuditLogController::class)
    ->name('audit_logs.')
    ->group(function () {
        Route::middleware(['permission:'.Permission::VIEW_AUDIT_LOGS->value])
            ->get('', 'index')
            ->name('index');

        Route::middleware(['permission:'.Permission::VIEW_AUDIT_LOGS->value])
            ->get('{id}', 'read')
            ->name('read');
    });
