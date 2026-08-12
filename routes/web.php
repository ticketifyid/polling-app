<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

// --- Publik: voter ---
Route::get('/', [VoteController::class, 'index'])->name('vote.index');
Route::post('/vote', [VoteController::class, 'store'])->name('vote.store');
Route::get('/vote/sukses', [VoteController::class, 'success'])->name('vote.success');

// --- Admin: auth ---
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// --- Admin: area terlindungi ---
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.kandidat.index'));

    Route::get('kandidat', [CandidateController::class, 'index'])->name('kandidat.index');
    Route::get('kandidat/create', [CandidateController::class, 'create'])->name('kandidat.create');
    Route::post('kandidat', [CandidateController::class, 'store'])->name('kandidat.store');
    Route::get('kandidat/{kandidat}/edit', [CandidateController::class, 'edit'])->name('kandidat.edit');
    Route::put('kandidat/{kandidat}', [CandidateController::class, 'update'])->name('kandidat.update');
    Route::post('kandidat/{kandidat}/toggle', [CandidateController::class, 'toggle'])->name('kandidat.toggle');

    Route::get('log', [LogController::class, 'index'])->name('log');

    // Hasil (videotron) — kini di belakang login admin.
    Route::get('hasil', [ResultController::class, 'index'])->name('result.index');

    Route::get('setting', [SettingController::class, 'edit'])->name('setting.edit');
    Route::put('setting', [SettingController::class, 'update'])->name('setting.update');
});
