<?php

use App\Http\Controllers\LeaveApprovalsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\LeaveBalance;
use App\Models\Leave;
use App\Http\Controllers\LeaveController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    $leaveBalances = LeaveBalance::where('user_id', $user->id)->first();
    $leaves = Leave::where('user_id', $user->id)->get();
    return Inertia::render('Dashboard', [
        'leaveBalances' => $leaveBalances,
        'leaves' => $leaves,
        'user' => $user,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');

    Route::get('/admin/approvals', [LeaveApprovalsController::class, 'index'])->name('leave-approvals.index');
    Route::delete('/admin/approvals/{leave}', [LeaveApprovalsController::class, 'destroy'])->name('leave-approvals.destroy');
    Route::get('/admin/approvals/{leave}/approve', [LeaveApprovalsController::class, 'approve'])->name('leave-approvals.approve');
    Route::get('/admin/approvals/{leave}/reject', [LeaveApprovalsController::class, 'reject'])->name('leave-approvals.reject');
});

require __DIR__.'/auth.php';
