<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\TendikController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\KelasController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//Route::get('/', [WelcomeController::class, 'index']);
Route::pattern('id', '[0-9]+'); //meaning: ketika ada parameter "id" maka nilainya harus angka, yaitu dari 0 sampai 9.
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postLogin']);

Route::middleware(['auth'])->group(function () {
    Route::get('/', [WelcomeController::class, 'index']);

    Route::group(['prefix' => 'jadwal', 'middleware' => 'authorize:ADM'], function () {
        Route::get('/', [JadwalController::class, 'index']); //Menampilkan laman awal jadwal
        Route::post('/list', [JadwalController::class, 'list']); //menampilkan data jadwal dalam bentuk json untuk datatables.

        Route::get('/create_ajax', [JadwalController::class, 'create_ajax']); //Buat data jadwal w ajax
        Route::post('/ajax', [JadwalController::class, 'store_ajax']); //menyimpan data jadwal baru w ajax

        Route::get('/{id}/show_ajax', [JadwalController::class, 'show_ajax']);

        Route::get('/{id}/edit_ajax', [JadwalController::class, 'edit_ajax']); //edit data jadwal dengan ajax
        Route::put('/{id}/update_ajax', [JadwalController::class, 'update_ajax']); //menyimpan perubahan data dengan ajax

        Route::get('/{id}/confirm_ajax', [JadwalController::class, 'confirm_ajax']); //Munculkan pop up konfirmasi delete dengan ajax
        Route::delete('/{id}/delete_ajax', [JadwalController::class, 'delete_ajax']); //Menghapus data jadwal dengan ajax
    });

    Route::group(['prefix' => 'ruangan', 'middleware' => 'authorize:ADM'], function () {
        Route::get('/', [RuanganController::class, 'index']); //Menampilkan laman awal ruangan
        Route::post('/list', [RuanganController::class, 'list']); //menampilkan data ruangan dalam bentuk json untuk datatables.

        Route::get('/create_ajax', [RuanganController::class, 'create_ajax']); //Buat data ruangan w ajax
        Route::post('/ajax', [RuanganController::class, 'store_ajax']); //menyimpan data ruangan baru w ajax

        Route::get('/{id}/show_ajax', [RuanganController::class, 'show_ajax']);

        Route::get('/{id}/edit_ajax', [RuanganController::class, 'edit_ajax']); //edit data ruangan dengan ajax
        Route::put('/{id}/update_ajax', [RuanganController::class, 'update_ajax']); //menyimpan perubahan data dengan ajax

        Route::get('/{id}/confirm_ajax', [RuanganController::class, 'confirm_ajax']); //Munculkan pop up konfirmasi delete dengan ajax
        Route::delete('/{id}/delete_ajax', [RuanganController::class, 'delete_ajax']); //Menghapus data ruangan dengan ajax
    });

    Route::group(['prefix' => 'admin', 'middleware' => 'authorize:ADM'], function () {
        Route::get('/', [AdminController::class, 'index']); //Menampilkan laman awal admin
        Route::post('/list', [AdminController::class, 'list']); //menampilkan data admin dalam bentuk json untuk datatables.

        Route::get('/create_ajax', [AdminController::class, 'create_ajax']); //Buat data admin w ajax
        Route::post('/ajax', [AdminController::class, 'store_ajax']); //menyimpan data admin baru w ajax

        Route::get('/{id}/show_ajax', [AdminController::class, 'show_ajax']);

        Route::get('/{id}/edit_ajax', [AdminController::class, 'edit_ajax']); //edit data admin dengan ajax
        Route::put('/{id}/update_ajax', [AdminController::class, 'update_ajax']); //menyimpan perubahan data dengan ajax

        Route::get('/{id}/confirm_ajax', [AdminController::class, 'confirm_ajax']); //Munculkan pop up konfirmasi delete dengan ajax
        Route::delete('/{id}/delete_ajax', [AdminController::class, 'delete_ajax']); //Menghapus data admin dengan ajax

        Route::get('/import', [AdminController::class, 'import']); //import excel
        Route::post('/import_ajax', [AdminController::class, 'import_ajax']); //import excel dengan ajax
    });

    Route::group(['prefix' => 'dosen', 'middleware' => 'authorize:ADM'], function () {
        Route::get('/', [DosenController::class, 'index']); //Menampilkan laman awal dosen
        Route::post('/list', [DosenController::class, 'list']); //menampilkan data dosen dalam bentuk json untuk datatables.

        Route::get('/create_ajax', [DosenController::class, 'create_ajax']); //Buat data dosen w ajax
        Route::post('/ajax', [DosenController::class, 'store_ajax']); //menyimpan data dosen baru w ajax

        Route::get('/{id}/show_ajax', [DosenController::class, 'show_ajax']);

        Route::get('/{id}/edit_ajax', [DosenController::class, 'edit_ajax']); //edit data dosen dengan ajax
        Route::put('/{id}/update_ajax', [DosenController::class, 'update_ajax']); //menyimpan perubahan data dengan ajax

        Route::get('/{id}/confirm_ajax', [DosenController::class, 'confirm_ajax']); //Munculkan pop up konfirmasi delete dengan ajax
        Route::delete('/{id}/delete_ajax', [DosenController::class, 'delete_ajax']); //Menghapus data dosen dengan ajax

        Route::get('/import', [DosenController::class, 'import']); //import excel
        Route::post('/import_ajax', [DosenController::class, 'import_ajax']); //import excel dengan ajax
    });

    Route::group(['prefix' => 'tendik', 'middleware' => 'authorize:ADM'], function () {
        Route::get('/', [TendikController::class, 'index']); //Menampilkan laman awal tendik
        Route::post('/list', [TendikController::class, 'list']); //menampilkan data tendik dalam bentuk json untuk datatables.

        Route::get('/create_ajax', [TendikController::class, 'create_ajax']); //Buat data tendik w ajax
        Route::post('/ajax', [TendikController::class, 'store_ajax']); //menyimpan data tendik baru w ajax

        Route::get('/{id}/show_ajax', [TendikController::class, 'show_ajax']);

        Route::get('/{id}/edit_ajax', [TendikController::class, 'edit_ajax']); //edit data tendik dengan ajax
        Route::put('/{id}/update_ajax', [TendikController::class, 'update_ajax']); //menyimpan perubahan data dengan ajax

        Route::get('/{id}/confirm_ajax', [TendikController::class, 'confirm_ajax']); //Munculkan pop up konfirmasi delete dengan ajax
        Route::delete('/{id}/delete_ajax', [TendikController::class, 'delete_ajax']); //Menghapus data tendik dengan ajax

        Route::get('/import', [TendikController::class, 'import']); //import excel
        Route::post('/import_ajax', [TendikController::class, 'import_ajax']); //import excel dengan ajax
    });

    Route::group(['prefix' => 'mahasiswa', 'middleware' => 'authorize:ADM'], function () {
        Route::get('/', [MahasiswaController::class, 'index']); //Menampilkan laman awal mahasiswa
        Route::post('/list', [MahasiswaController::class, 'list']); //menampilkan data mahasiswa dalam bentuk json untuk datatables.

        Route::get('/create_ajax', [MahasiswaController::class, 'create_ajax']); //Buat data mahasiswa w ajax
        Route::post('/ajax', [MahasiswaController::class, 'store_ajax']); //menyimpan data mahasiswa baru w ajax

        Route::get('/{id}/show_ajax', [MahasiswaController::class, 'show_ajax']);

        Route::get('/{id}/edit_ajax', [MahasiswaController::class, 'edit_ajax']); //edit data mahasiswa dengan ajax
        Route::put('/{id}/update_ajax', [MahasiswaController::class, 'update_ajax']); //menyimpan perubahan data dengan ajax

        Route::get('/{id}/confirm_ajax', [MahasiswaController::class, 'confirm_ajax']); //Munculkan pop up konfirmasi delete dengan ajax
        Route::delete('/{id}/delete_ajax', [MahasiswaController::class, 'delete_ajax']); //Menghapus data mahasiswa dengan ajax

        Route::get('/import', [MahasiswaController::class, 'import']); //import excel
        Route::post('/import_ajax', [MahasiswaController::class, 'import_ajax']); //import excel dengan ajax
    });

    Route::group(['prefix' => 'prodi', 'middleware' => 'authorize:ADM'], function () {
        Route::get('/', [ProdiController::class, 'index']); //Menampilkan laman awal prodi
        Route::post('/list', [ProdiController::class, 'list']); //menampilkan data prodi dalam bentuk json untuk datatables.

        Route::get('/create_ajax', [ProdiController::class, 'create_ajax']); //Buat data prodi w ajax
        Route::post('/ajax', [ProdiController::class, 'store_ajax']); //menyimpan data prodi baru w ajax

        Route::get('/{id}/edit_ajax', [ProdiController::class, 'edit_ajax']); //edit data prodi dengan ajax
        Route::put('/{id}/update_ajax', [ProdiController::class, 'update_ajax']); //menyimpan perubahan data dengan ajax

        Route::get('/{id}/confirm_ajax', [ProdiController::class, 'confirm_ajax']); //Munculkan pop up konfirmasi delete dengan ajax
        Route::delete('/{id}/delete_ajax', [ProdiController::class, 'delete_ajax']); //Menghapus data prodi dengan ajax
    });

    Route::group(['prefix' => 'kelas', 'middleware' => 'authorize:ADM'], function () {
        Route::get('/', [KelasController::class, 'index']); //Menampilkan laman awal kelas
        Route::post('/list', [KelasController::class, 'list']); //menampilkan data kelas dalam bentuk json untuk datatables.

        Route::get('/create_ajax', [KelasController::class, 'create_ajax']); //Buat data kelas w ajax
        Route::post('/ajax', [KelasController::class, 'store_ajax']); //menyimpan data kelas baru w ajax

        Route::get('/{id}/edit_ajax', [KelasController::class, 'edit_ajax']); //edit data kelas dengan ajax
        Route::put('/{id}/update_ajax', [KelasController::class, 'update_ajax']); //menyimpan perubahan data dengan ajax

        Route::get('/{id}/confirm_ajax', [KelasController::class, 'confirm_ajax']); //Munculkan pop up konfirmasi delete dengan ajax
        Route::delete('/{id}/delete_ajax', [KelasController::class, 'delete_ajax']); //Menghapus data kelas dengan ajax
    });
});

