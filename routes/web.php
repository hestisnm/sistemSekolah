<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\adminController;
use App\Http\Controllers\siswaController;
use App\Http\Controllers\kontenController;
use App\Http\Controllers\KbmController;
use App\Http\Middleware\CekLogin;


Route::get('/', [kontenController::class, 'landing'])->name('landing');

// LOGIN & LOGOUT
Route::get('/login', [adminController::class, 'formLogin'])->name('login');
Route::post('/login', [adminController::class, 'loginPost'])->name('login.post');
Route::get('/logout', [adminController::class, 'logout'])->name('logout');


// HOME (semua role masuk lewat sini)
Route::middleware(['CekLogin'])->group(function () {
    Route::get('/home', [adminController::class, 'index'])->name('home');
});


// REGISTER
Route::get('/register', [adminController::class, 'formRegister'])->name('register');
Route::post('/register', [adminController::class, 'prosesRegister'])->name('register.post');

// SISWA CRUD
// SISWA CRUD (hanya admin yg boleh)
Route::middleware(['CekLogin:admin'])->group(function () {

 Route::get('/siswa/create', [siswaController::class, 'create'])
    ->name('siswa.create');

Route::post('/siswa/store', [siswaController::class, 'store'])
    ->name('siswa.store');

Route::get('/siswa/edit/{id}', [siswaController::class, 'edit'])
    ->name('siswa.edit');

Route::put('/siswa/update/{siswa}', [SiswaController::class, 'update'])
    ->name('siswa.update');

Route::delete('/siswa/delete/{siswa}', [SiswaController::class, 'destroy'])
    ->name('siswa.delete');

Route::get('/siswa/data', [SiswaController::class, 'getData'])
    ->name('siswa.data');

Route::get('/siswa/search', [SiswaController::class, 'search'])
    ->name('siswa.search');

});


Route::get('/detil/{id}', [kontenController::class, 'detil'])->name('detil');

Route::post('/kbm', [kbmController::class, 'store'])->name('kbm.store');
Route::get('/kbm', [KbmController::class, 'index'])->name('kbm.index');
Route::get('/kbm/guru/{idguru}', [KbmController::class, 'showByGuru'])->name('kbm.byGuru');
Route::get('/kbm/kelas/{idwalas}', [KbmController::class, 'showByKelas'])->name('kbm.byKelas');
Route::get('/kbm/data', [KbmController::class, 'getJadwal'])->name('kbm.data')->middleware('CekLogin');


