<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\EmployeeController;
use App\Http\Controllers\Owner\AttendanceController as OwnerAttendanceController;
use App\Http\Controllers\Owner\LeaveController as OwnerLeaveController;
use App\Http\Controllers\Owner\SalaryController as OwnerSalaryController;
use App\Http\Controllers\Owner\SettingController;
use App\Http\Controllers\Owner\ShiftController as OwnerShiftController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use App\Http\Controllers\Karyawan\AttendanceController as KaryawanAttendanceController;
use App\Http\Controllers\Karyawan\LeaveController as KaryawanLeaveController;
use App\Http\Controllers\Karyawan\SalaryController as KaryawanSalaryController;
use App\Http\Controllers\Karyawan\ProfileController as KaryawanProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============================================
// PUBLIC ROUTES
// ============================================

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'owner' 
            ? redirect()->route('owner.dashboard') 
            : redirect()->route('karyawan.dashboard');
    }
    return redirect()->route('login');
});

// ============================================
// AUTHENTICATION ROUTES
// ============================================




// Optional: Registration routes (if needed)
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ============================================
// OWNER ROUTES (ROLE: OWNER)
// ============================================

Route::prefix('owner')
    ->middleware(['auth', 'role:owner'])
    ->name('owner.')
    ->group(function () {
        
        // ========== DASHBOARD ==========
        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [OwnerDashboardController::class, 'chartData'])->name('dashboard.chart');
        Route::get('/dashboard/recent-activities', [OwnerDashboardController::class, 'recentActivities'])->name('dashboard.activities');
        
        // ========== EMPLOYEE MANAGEMENT ==========
        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('/', [EmployeeController::class, 'index'])->name('index');
            Route::get('/create', [EmployeeController::class, 'create'])->name('create');
            Route::post('/', [EmployeeController::class, 'store'])->name('store');
            Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
            Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
            Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
            Route::get('/{employee}/profile', [EmployeeController::class, 'show'])->name('show');
            Route::post('/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/import', [EmployeeController::class, 'import'])->name('import');
            Route::get('/export', [EmployeeController::class, 'export'])->name('export');
        });

        // ========== ATTENDANCE MANAGEMENT ==========
        Route::prefix('attendances')->name('attendances.')->group(function () {
            Route::get('/', [OwnerAttendanceController::class, 'index'])->name('index');
            Route::get('/{attendance}', [OwnerAttendanceController::class, 'show'])->name('show');
            Route::post('/{attendance}/verify', [OwnerAttendanceController::class, 'verify'])->name('verify');
            Route::post('/{attendance}/unverify', [OwnerAttendanceController::class, 'unverify'])->name('unverify');
            Route::post('/bulk-verify', [OwnerAttendanceController::class, 'bulkVerify'])->name('bulk-verify');
            Route::post('/bulk-delete', [OwnerAttendanceController::class, 'bulkDelete'])->name('bulk-delete');
            Route::get('/export', [OwnerAttendanceController::class, 'export'])->name('export');
            Route::get('/report', [OwnerAttendanceController::class, 'report'])->name('report');
            Route::get('/report/pdf', [OwnerAttendanceController::class, 'exportPdf'])->name('report.pdf');
            Route::get('/summary', [OwnerAttendanceController::class, 'summary'])->name('summary');
        });

        // ========== LEAVE MANAGEMENT ==========
        Route::prefix('leaves')->name('leaves.')->group(function () {
            Route::get('/', [OwnerLeaveController::class, 'index'])->name('index');
            Route::get('/{leave}', [OwnerLeaveController::class, 'show'])->name('show');
            Route::post('/{leave}/approve', [OwnerLeaveController::class, 'approve'])->name('approve');
            Route::post('/{leave}/reject', [OwnerLeaveController::class, 'reject'])->name('reject');
            Route::post('/{leave}/cancel', [OwnerLeaveController::class, 'cancel'])->name('cancel');
            Route::get('/export', [OwnerLeaveController::class, 'export'])->name('export');
            Route::get('/calendar', [OwnerLeaveController::class, 'calendar'])->name('calendar');
        });

        // ========== SALARY MANAGEMENT ==========
        Route::prefix('salaries')->name('salaries.')->group(function () {
            Route::get('/', [OwnerSalaryController::class, 'index'])->name('index');
            Route::get('/{salary}', [OwnerSalaryController::class, 'show'])->name('show');
            Route::post('/calculate', [OwnerSalaryController::class, 'calculate'])->name('calculate');
            Route::post('/calculate/{period}', [OwnerSalaryController::class, 'calculatePeriod'])->name('calculate-period');
            Route::post('/{salary}/approve', [OwnerSalaryController::class, 'approve'])->name('approve');
            Route::post('/{salary}/paid', [OwnerSalaryController::class, 'markAsPaid'])->name('paid');
            Route::get('/{salary}/payslip', [OwnerSalaryController::class, 'payslip'])->name('payslip');
            Route::get('/{salary}/payslip/pdf', [OwnerSalaryController::class, 'payslipPdf'])->name('payslip.pdf');
            Route::post('/{salary}/notes', [OwnerSalaryController::class, 'updateNotes'])->name('notes');
            Route::get('/report', [OwnerSalaryController::class, 'report'])->name('report');
            Route::get('/export', [OwnerSalaryController::class, 'export'])->name('export');
        });

        // ========== SHIFT MANAGEMENT ==========
        Route::prefix('shifts')->name('shifts.')->group(function () {
            Route::get('/', [OwnerShiftController::class, 'index'])->name('index');
            Route::get('/create', [OwnerShiftController::class, 'create'])->name('create');
            Route::post('/', [OwnerShiftController::class, 'store'])->name('store');
            Route::get('/{shift}/edit', [OwnerShiftController::class, 'edit'])->name('edit');
            Route::put('/{shift}', [OwnerShiftController::class, 'update'])->name('update');
            Route::delete('/{shift}', [OwnerShiftController::class, 'destroy'])->name('destroy');
            Route::post('/{shift}/toggle', [OwnerShiftController::class, 'toggleStatus'])->name('toggle');
            Route::post('/{shift}/assign', [OwnerShiftController::class, 'assignEmployee'])->name('assign');
            Route::post('/{shift}/unassign', [OwnerShiftController::class, 'unassignEmployee'])->name('unassign');
            Route::get('/schedule', [OwnerShiftController::class, 'schedule'])->name('schedule');
            Route::post('/schedule/generate', [OwnerShiftController::class, 'generateSchedule'])->name('schedule.generate');
        });

        // ========== SETTINGS ==========
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::post('/', [SettingController::class, 'update'])->name('update');
            Route::post('/location', [SettingController::class, 'updateLocation'])->name('location');
            Route::post('/office-hours', [SettingController::class, 'updateOfficeHours'])->name('office-hours');
            Route::post('/overtime', [SettingController::class, 'updateOvertime'])->name('overtime');
            Route::post('/backup', [SettingController::class, 'backup'])->name('backup');
            Route::get('/backup/download', [SettingController::class, 'downloadBackup'])->name('backup.download');
        });

        // ========== REPORTS ==========
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/attendance', [OwnerAttendanceController::class, 'report'])->name('attendance');
            Route::get('/attendance/pdf', [OwnerAttendanceController::class, 'exportPdf'])->name('attendance.pdf');
            Route::get('/salary', [OwnerSalaryController::class, 'report'])->name('salary');
            Route::get('/salary/pdf', [OwnerSalaryController::class, 'exportPdf'])->name('salary.pdf');
            Route::get('/leave', [OwnerLeaveController::class, 'report'])->name('leave');
            Route::get('/employee', [EmployeeController::class, 'report'])->name('employee');
        });

        // ========== NOTIFICATIONS ==========
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [OwnerDashboardController::class, 'notifications'])->name('index');
            Route::post('/{id}/read', [OwnerDashboardController::class, 'markAsRead'])->name('read');
            Route::post('/read-all', [OwnerDashboardController::class, 'markAllAsRead'])->name('read-all');
        });
    });

// ============================================
// KARYAWAN ROUTES (ROLE: KARYAWAN)
// ============================================

Route::prefix('karyawan')
    ->middleware(['auth', 'role:karyawan'])
    ->name('karyawan.')
    ->group(function () {
        
        // ========== DASHBOARD ==========
        Route::get('/dashboard', [KaryawanDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [KaryawanDashboardController::class, 'stats'])->name('dashboard.stats');
        Route::get('/dashboard/attendance-chart', [KaryawanDashboardController::class, 'attendanceChart'])->name('dashboard.chart');

        // ========== ATTENDANCE ==========
        Route::prefix('attendances')->name('attendances.')->group(function () {
            Route::get('/', [KaryawanAttendanceController::class, 'index'])->name('index');
            Route::post('/checkin', [KaryawanAttendanceController::class, 'checkIn'])->name('checkin');
            Route::post('/checkout', [KaryawanAttendanceController::class, 'checkOut'])->name('checkout');
            Route::get('/history', [KaryawanAttendanceController::class, 'history'])->name('history');
            Route::get('/{attendance}', [KaryawanAttendanceController::class, 'show'])->name('show');
            Route::post('/{attendance}/request-verification', [KaryawanAttendanceController::class, 'requestVerification'])->name('request-verification');
            Route::get('/calendar', [KaryawanAttendanceController::class, 'calendar'])->name('calendar');
            Route::get('/export', [KaryawanAttendanceController::class, 'export'])->name('export');
        });

        // ========== LEAVE ==========
        Route::prefix('leaves')->name('leaves.')->group(function () {
            Route::get('/', [KaryawanLeaveController::class, 'index'])->name('index');
            Route::get('/create', [KaryawanLeaveController::class, 'create'])->name('create');
            Route::post('/', [KaryawanLeaveController::class, 'store'])->name('store');
            Route::get('/{leave}', [KaryawanLeaveController::class, 'show'])->name('show');
            Route::delete('/{leave}', [KaryawanLeaveController::class, 'destroy'])->name('destroy');
            Route::get('/balance', [KaryawanLeaveController::class, 'balance'])->name('balance');
            Route::post('/{leave}/cancel', [KaryawanLeaveController::class, 'cancel'])->name('cancel');
        });

        // ========== SALARY ==========
        Route::prefix('salary')->name('salary.')->group(function () {
            Route::get('/', [KaryawanSalaryController::class, 'index'])->name('index');
            Route::get('/{salary}', [KaryawanSalaryController::class, 'show'])->name('show');
            Route::get('/{salary}/payslip', [KaryawanSalaryController::class, 'payslip'])->name('payslip');
            Route::get('/{salary}/payslip/pdf', [KaryawanSalaryController::class, 'payslipPdf'])->name('payslip.pdf');
            Route::get('/summary', [KaryawanSalaryController::class, 'summary'])->name('summary');
            Route::get('/history', [KaryawanSalaryController::class, 'history'])->name('history');
        });

        // ========== PROFILE ==========
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [KaryawanProfileController::class, 'index'])->name('index');
            Route::put('/', [KaryawanProfileController::class, 'update'])->name('update');
            Route::post('/change-password', [KaryawanProfileController::class, 'changePassword'])->name('change-password');
            Route::post('/upload-photo', [KaryawanProfileController::class, 'uploadPhoto'])->name('upload-photo');
            Route::delete('/photo', [KaryawanProfileController::class, 'deletePhoto'])->name('delete-photo');
        });

        // ========== NOTIFICATIONS ==========
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [KaryawanDashboardController::class, 'notifications'])->name('index');
            Route::post('/{id}/read', [KaryawanDashboardController::class, 'markAsRead'])->name('read');
            Route::post('/read-all', [KaryawanDashboardController::class, 'markAllAsRead'])->name('read-all');
        });
    });

// ============================================
// API ROUTES (Optional - for AJAX/SPA)
// ============================================

Route::prefix('api')->middleware(['auth'])->name('api.')->group(function () {
    
    // ========== ATTENDANCE API ==========
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::post('/checkin', [KaryawanAttendanceController::class, 'apiCheckIn'])->name('checkin');
        Route::post('/checkout', [KaryawanAttendanceController::class, 'apiCheckOut'])->name('checkout');
        Route::get('/today', [KaryawanAttendanceController::class, 'apiToday'])->name('today');
        Route::get('/monthly', [KaryawanAttendanceController::class, 'apiMonthly'])->name('monthly');
    });

    // ========== EMPLOYEE API ==========
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'apiIndex'])->name('index');
        Route::get('/{employee}', [EmployeeController::class, 'apiShow'])->name('show');
        Route::get('/search', [EmployeeController::class, 'apiSearch'])->name('search');
    });

    // ========== LEAVE API ==========
    Route::prefix('leaves')->name('leaves.')->group(function () {
        Route::post('/', [KaryawanLeaveController::class, 'apiStore'])->name('store');
        Route::get('/balance', [KaryawanLeaveController::class, 'apiBalance'])->name('balance');
    });

    // ========== SALARY API ==========
    Route::prefix('salaries')->name('salaries.')->group(function () {
        Route::get('/current', [KaryawanSalaryController::class, 'apiCurrent'])->name('current');
        Route::get('/history', [KaryawanSalaryController::class, 'apiHistory'])->name('history');
    });

    // ========== DASHBOARD API ==========
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/stats', [OwnerDashboardController::class, 'apiStats'])->name('stats');
        Route::get('/chart', [OwnerDashboardController::class, 'apiChart'])->name('chart');
        Route::get('/activities', [OwnerDashboardController::class, 'apiActivities'])->name('activities');
    });
});

// ============================================
// WEBHOOKS ROUTES (Optional - for integrations)
// ============================================

Route::prefix('webhooks')->name('webhooks.')->group(function () {
    // For external integrations (Slack, Telegram, etc.)
    Route::post('/telegram', [WebhookController::class, 'telegram'])->name('telegram');
    Route::post('/slack', [WebhookController::class, 'slack'])->name('slack');
});

// ============================================
// FALLBACK ROUTE (404 Handler)
// ============================================

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

// ============================================
// TEST ROUTES (Development only)
// ============================================

if (app()->environment('local')) {
    Route::prefix('test')->name('test.')->group(function () {
        Route::get('/email', function () {
            return view('emails.test');
        })->name('email');
        
        Route::get('/error', function () {
            abort(500, 'Test error');
        })->name('error');
    });
}