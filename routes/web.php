<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiberkasController;

// === HALAMAN PUBLIK (USER) ===
Route::get('/', [SiberkasController::class, 'index'])->name('home');
Route::post('/kirim', [SiberkasController::class, 'store'])->name('kirim');

// === HALAMAN AUTH (LOGIN/LOGOUT) ===
Route::get('/login', [SiberkasController::class, 'showLogin'])->name('login');
Route::post('/login', [SiberkasController::class, 'authenticate'])->name('login.proses');
Route::post('/logout', [SiberkasController::class, 'logout'])->name('logout');

// === HALAMAN ADMIN (WAJIB LOGIN) ===
Route::middleware('auth')->prefix('admin')->group(function() {
    
    // Dashboard Utama
    Route::get('/', [SiberkasController::class, 'admin'])->name('admin.dashboard');
    
    // Fitur Update Data Pemohon
    Route::put('/update-data/{no_permohonan}', [SiberkasController::class, 'updateData'])->name('admin.update_data');
    
    // === [BARU] Fitur Checklist (Tandai Sudah Dicek) ===
    Route::patch('/toggle-check/{no_permohonan}', [SiberkasController::class, 'toggleCheck'])->name('admin.toggle_check');
    
    // Fitur Ganti Background
    Route::post('/update-bg', [SiberkasController::class, 'updateBackground'])->name('admin.bg');
    
    // Fitur Hapus (Delete)
    Route::delete('/hapus/{id}', [SiberkasController::class, 'destroy'])->name('admin.hapus');
    Route::delete('/hapus-bulan', [SiberkasController::class, 'destroyMonth'])->name('admin.hapus_bulan');
});