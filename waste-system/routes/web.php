<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RouteController as AdminRouteController;
use App\Http\Controllers\Citizen\PickupRequestController as CitizenPickupRequestController;
use App\Http\Controllers\Driver\RouteController as DriverRouteController;
use App\Http\Controllers\Driver\NotificationController as DriverNotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = auth()->user();

    if ($user) {
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('driver')) {
            return redirect()->route('driver.routes.today');
        }

        return redirect()->route('citizen.dashboard');
    }

    return view('auth.landing');
})->name('home');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user?->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user?->hasRole('driver')) {
        return redirect()->route('driver.routes.today');
    }

    return redirect()->route('citizen.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:citizen'])->prefix('citizen')->name('citizen.')->group(function () {
    Route::get('/dashboard', [CitizenPickupRequestController::class, 'index'])->name('dashboard');
    Route::get('/requests/create', [CitizenPickupRequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [CitizenPickupRequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{id}', [CitizenPickupRequestController::class, 'show'])->name('requests.show');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/requests', [AdminDashboardController::class, 'requests'])->name('requests');
    Route::get('/routes', [AdminRouteController::class, 'index'])->name('routes.index');
    Route::get('/routes/create', [AdminRouteController::class, 'create'])->name('routes.create');
    Route::post('/routes', [AdminRouteController::class, 'store'])->name('routes.store');
    Route::get('/routes/{id}', [AdminRouteController::class, 'show'])->name('routes.show');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
    Route::get('/export/csv', [AdminDashboardController::class, 'exportCsv'])->name('export.csv');
});

Route::middleware(['auth', 'role:driver'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/routes/today', [DriverRouteController::class, 'today'])->name('routes.today');
    Route::get('/routes/{id}/stops', [DriverRouteController::class, 'stops'])->name('routes.stops');
    Route::get('/collect/{requestId}', [DriverRouteController::class, 'collectForm'])->name('collect.form');
    Route::post('/collect/{requestId}', [DriverRouteController::class, 'collect'])->name('collect');
    
    // Notification routes
    Route::get('/notifications', [DriverNotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [DriverNotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/{id}/mark-read', [DriverNotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [DriverNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
});

require __DIR__.'/auth.php';
