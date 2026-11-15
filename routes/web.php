<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [WebController::class, 'dashboard'])->name('dashboard');
    Route::get('/riwayat/export-pdf', [WebController::class, 'exportPdf'])->name('riwayat.pdf');
    Route::get('/riwayat', [WebController::class, 'riwayat'])->name('riwayat');
    Route::get('/notifikasi', [WebController::class, 'notifikasi'])->name('notifikasi');
    Route::get('/perangkat', [WebController::class, 'perangkat'])->name('perangkat');
    Route::get('/profil', [WebController::class, 'profil'])->name('profil');
});
