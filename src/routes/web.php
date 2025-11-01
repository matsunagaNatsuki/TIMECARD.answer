<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MiddlewareController;
use App\Http\Middleware\AdminStatusMiddleware;
use Laravel\Fortify\Http\Controllers\VerifyEmailController;
use Illuminate\Http\Request;
use App\Http\Requests\CorrectionRequest;


Route::middleware('auth')->group(function () {
    Route::get('/attendance', [UserController::class, 'index']);
    Route::post('/attendance', [UserController::class, 'attendance']);
    Route::get('/attendance/list', [UserController::class, 'list']);
    Route::get('/application/{id}', [UserController::class, 'applicationDetail']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/attendance/list', [AdminController::class, 'list']);
    Route::get('/admin/staff/list', [AdminController::class, 'staffList']);
    Route::get('/admin/attendance/staff/{id}', [AdminController::class, 'staffDetailList']);
    Route::post('/admin/logout', [AuthController::class, 'adminLogout']);
    Route::get('/stamp_correction_request/approval/{id}', [AdminController::class, 'approvalShow']);
    Route::post('/stamp_correction_request/approval/{id}', [AdminController::class, 'approval']);
    Route::post('/export', [AdminController::class, 'export']);
});

Route::middleware(['auth', AdminStatusMiddleware::class])->group(function () {
    Route::get('/stamp_correction_request/list', function (Request $request) {
        if ($request->headers->has('referer') && str_contains ($request->headers->get('referer'), '/admin')) {
            if (auth()->user()->admin_status) {
                return app(AdminController::class)->applicationList($request);
            }
        } else {
            return app(UserController::class)->applicationList($request);
        }
    });
    Route::get('/attendance/{id}', function ($id, Request $request) {
        if ($request->headers->has('referer') && str_contains ($request->headers->get('referer'), 'admin')) {
            if (auth()->user()->admin_status) {
                return app(AdminController::class)->detail($id);
            }
        } else {
            return app(UserController::class)->detail($id);
        }
    });
    Route::post('/attendance/{id}', function (CorrectionRequest $request, $id) {
        if (auth()->user()->admin_status) {
            if (auth()->user()->admin_status) {
                return app(AdminController::class)
                ->amendmentApplication($request, $id);
            }
        } else {
            return app(UserController::class)->amendmentApplication($request, $id);
        }
    });
});

Route::get('admin/login', [AuthController::class, 'adminLogin']);
Route::post('/admin/login', [AuthController::class, 'adminDoLogin']);

Route::post('/login', [AuthController::class, 'doLogin']);
Route::post('/logout', [AuthController::class, 'doLogout']);
Route::post('/register', [AuthController::class, 'store']);
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware(['auth'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed'])
    ->name('verification.verify');
























// Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
//   ->middleware(['auth', 'signed', 'throttle:6,1'])
//   ->name('verification.verify');