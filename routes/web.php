<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\adminController;
use App\Http\Controllers\siswaController;
use App\Http\Controllers\kontenController;
use App\Http\Controllers\KbmController;

Route::get('/', [kontenController::class, 'landing'])->name('landing');

// LOGIN & LOGOUT
Route::get('/login', [adminController::class, 'formLogin'])->name('login');
Route::post('/login', [adminController::class, 'loginPost'])->name('login.post');
Route::get('/logout', [adminController::class, 'logout'])->name('logout');

// HOME (semua role masuk lewat sini)
Route::get('/home', [adminController::class, 'index'])->name('home');

// REGISTER
Route::get('/register', [adminController::class, 'formRegister'])->name('register');
Route::post('/register', [adminController::class, 'prosesRegister'])->name('register.post');

// SISWA CRUD
Route::get('/siswa/create', [siswaController::class, 'create'])->name('siswa.create');
Route::post('/siswa/store', [siswaController::class, 'store'])->name('siswa.store');
Route::get('/siswa/{id}/edit', [siswaController::class, 'edit'])->name('siswa.edit');
Route::post('/siswa/{id}/update', [siswaController::class, 'update'])->name('siswa.update');
Route::get('/siswa/{id}/delete', [siswaController::class, 'destroy'])->name('siswa.delete');

Route::get('/detil/{id}', [kontenController::class, 'detil'])->name('detil');

Route::post('/kbm', [kbmController::class, 'store'])->name('kbm.store');
Route::get('/kbm', [KbmController::class, 'index'])->name('kbm.index');
Route::get('/kbm/guru/{idguru}', [KbmController::class, 'showByGuru'])->name('kbm.byGuru');
Route::get('/kbm/kelas/{idwalas}', [KbmController::class, 'showByKelas'])->name('kbm.byKelas');
