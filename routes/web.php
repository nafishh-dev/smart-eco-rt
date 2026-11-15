<?php

use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WebController::class, 'dashboard'])->name('dashboard');
Route::get('/riwayat/export-pdf', [WebController::class, 'exportPdf'])->name('riwayat.pdf');
Route::get('/riwayat', [WebController::class, 'riwayat'])->name('riwayat');
Route::get('/notifikasi', [WebController::class, 'notifikasi'])->name('notifikasi');
Route::get('/perangkat', [WebController::class, 'perangkat'])->name('perangkat');
