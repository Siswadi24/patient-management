<?php

use App\Http\Controllers\CategoryTransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::prefix('user')->name('user.')->middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard', [
            'user' => auth()->user()
        ]);
    })->name('dashboard');

    Route::resource('categories', CategoryTransactionController::class);
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [TransactionController::class, 'edit'])->name('edit');
        Route::match(['PUT', 'POST'], '/{id}', [TransactionController::class, 'update'])->name('update');
        Route::delete('/{id}', [TransactionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('dashboard-analytics')->name('dashboard-analytics.')->group(function () {
        Route::get('/daily', [DashboardController::class, 'daily'])->name('daily');
        Route::get('/weekly', [DashboardController::class, 'weekly'])->name('weekly');
        Route::get('/monthly', [DashboardController::class, 'monthly'])->name('monthly');
        Route::get('/yearly', [DashboardController::class, 'yearly'])->name('yearly');

        Route::get('/chart/daily', [DashboardController::class, 'chartDaily'])->name('chart.daily');
        Route::get('/chart/weekly', [DashboardController::class, 'chartWeekly'])->name('chart.weekly');
        Route::get('/chart/monthly', [DashboardController::class, 'chartMonthly'])->name('chart.monthly');
        Route::get('/chart/yearly', [DashboardController::class, 'chartYearly'])->name('chart.yearly');
        Route::get('/categories', [DashboardController::class, 'categoryAnalytics'])->name('categories');
    });

    Route::prefix('patient')->name('patient.')->group(function () {
        Route::get('/', [PatientController::class, 'index'])->name('records');
        Route::post('/store-patient', [PatientController::class, 'storePatient'])->name('storePatient');
    });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/auth.php';
