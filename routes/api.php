<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
    Route::post('login', 'App\Http\Controllers\AuthController@login');
    Route::get('instruktur', 'App\Http\Controllers\InstrukturController@index');
    Route::post('instruktur', 'App\Http\Controllers\InstrukturController@store');
    Route::get('instruktur/{id_instruktur}', 'App\Http\Controllers\InstrukturController@show');
    Route::put('instruktur/{id_instruktur}', 'App\Http\Controllers\InstrukturController@update');
    Route::delete('instruktur/{id_instruktur}', 'App\Http\Controllers\InstrukturController@destroy');
    Route::post('instruktur_reset', 'App\Http\Controllers\InstrukturController@reset');


    Route::get('member', 'App\Http\Controllers\MemberController@index');
    Route::post('member', 'App\Http\Controllers\MemberController@store');
    Route::get('member/{id_member}', 'App\Http\Controllers\MemberController@show');
    Route::put('member/{id_member}', 'App\Http\Controllers\MemberController@update');
    Route::post('member/{id_member}', 'App\Http\Controllers\MemberController@resetPassword');
    Route::post('member_exp/{id_member}', 'App\Http\Controllers\MemberController@deaktivasi');
    Route::get('member_show', 'App\Http\Controllers\MemberController@showTglExp');
    Route::delete('member/{id_member}', 'App\Http\Controllers\MemberController@destroy');
    Route::get('showDepoK/{id_member}', 'App\Http\Controllers\MemberController@showDepoK');

    Route::get('pegawai', 'App\Http\Controllers\PegawaiController@index');
    Route::post('pegawai', 'App\Http\Controllers\PegawaiController@store');
    Route::get('pegawai/{id_pegawai}', 'App\Http\Controllers\PegawaiController@show');
    Route::put('pegawai/{id_pegawai}', 'App\Http\Controllers\PegawaiController@update');
    Route::delete('pegawai/{id_pegawai}', 'App\Http\Controllers\PegawaiController@destroy');

    Route::get('kelas', 'App\Http\Controllers\KelasController@index');
    Route::post('kelas', 'App\Http\Controllers\KelasController@store');
    Route::get('kelas/{id_kelas}', 'App\Http\Controllers\KelasController@show');
    Route::put('kelas/{id_kelas}', 'App\Http\Controllers\KelasController@update');
    Route::delete('kelas/{id_kelas}', 'App\Http\Controllers\KelasController@destroy');

    Route::get('jadwalU', 'App\Http\Controllers\JadwalUController@index');
    Route::post('jadwalU', 'App\Http\Controllers\JadwalUController@store');
    Route::get('jadwalU/{id_jadwalU}', 'App\Http\Controllers\JadwalUController@show');
    Route::put('jadwalU/{id_jadwalU}', 'App\Http\Controllers\JadwalUController@update');
    Route::delete('jadwalU/{id_jadwalU}', 'App\Http\Controllers\JadwalUController@destroy');

    Route::get('jadwalH', 'App\Http\Controllers\JadwalHController@index');
    Route::post('jadwalH', 'App\Http\Controllers\JadwalHController@store');
    Route::get('jadwalH/{id_jadwalH}', 'App\Http\Controllers\JadwalHController@show');
    Route::put('jadwalH/{id_jadwalH}', 'App\Http\Controllers\JadwalHController@update');
    // Route::delete('jadwalH/{id_jadwalH}', 'App\Http\Controllers\JadwalHController@destroy');

    Route::get('aktivasi', 'App\Http\Controllers\AktivasiController@index');
    Route::post('aktivasi', 'App\Http\Controllers\AktivasiController@store');
    Route::get('aktivasi/{id_aktivasi}', 'App\Http\Controllers\AktivasiController@show');

    Route::get('depoU', 'App\Http\Controllers\TransaksiDepoU@index');
    Route::post('depoU', 'App\Http\Controllers\TransaksiDepoU@store');
    Route::get('depoU/{id_Tdepou}', 'App\Http\Controllers\TransaksiDepoU@show');

    Route::get('depoK', 'App\Http\Controllers\TransaksiDepoK@index');
    Route::get('depoK_show', 'App\Http\Controllers\TransaksiDepoK@showTglExp');
    Route::post('depoK', 'App\Http\Controllers\TransaksiDepoK@store');
    Route::post('depoK/{id_TdepoK}', 'App\Http\Controllers\TransaksiDepoK@resetDepoK');
    Route::get('depoK/{id_TdepoK}', 'App\Http\Controllers\TransaksiDepoK@show');

    Route::get('perizinan', 'App\Http\Controllers\PerizinanController@index');
    Route::get('show_izin', 'App\Http\Controllers\PerizinanController@show_izin');
    Route::post('perizinan', 'App\Http\Controllers\PerizinanController@store');
    Route::post('perizinan/{id_perizinan}', 'App\Http\Controllers\PerizinanController@konfirmasi');
    Route::get('perizinan/{id_perizinan}', 'App\Http\Controllers\PerizinanController@show');
    Route::get('show_instruktur/{id_instruktur}', 'App\Http\Controllers\PerizinanController@showInstruktur');

    Route::get('bookingK', 'App\Http\Controllers\BookingKelasController@index');
    Route::post('bookingK', 'App\Http\Controllers\BookingKelasController@store');
    Route::get('bookingK/{id_booking}', 'App\Http\Controllers\BookingKelasController@show');
    Route::post('bookingK/{id_booking}', 'App\Http\Controllers\BookingKelasController@cancelBooking');
    Route::post('konfirmasiK/{id_booking}', 'App\Http\Controllers\BookingKelasController@konfirmasiHadir');
    Route::get('showBelumK', 'App\Http\Controllers\BookingKelasController@showBelum');
    Route::get('showSudahK', 'App\Http\Controllers\BookingKelasController@showSudah');

    Route::get('bookingG', 'App\Http\Controllers\BookingGymController@index');
    Route::post('bookingG', 'App\Http\Controllers\BookingGymController@store');
    Route::get('bookingG/{id_booking}', 'App\Http\Controllers\BookingGymController@show');
    Route::post('bookingG/{id_booking}', 'App\Http\Controllers\BookingGymController@cancelBooking');
    Route::post('konfirmasiG/{id_booking}', 'App\Http\Controllers\BookingGymController@konfirmasiPresensi');
    Route::get('showBelum', 'App\Http\Controllers\BookingGymController@showBelum');
    Route::get('showSudah', 'App\Http\Controllers\BookingGymController@showSudah');

    Route::get('presensiI', 'App\Http\Controllers\PresensiInstrukturController@showSchadule');
    Route::post('presensiI/{id_jadwalH}', 'App\Http\Controllers\PresensiInstrukturController@store');
    Route::post('jam_selesai/{id_presensi_instruktur}', 'App\Http\Controllers\PresensiInstrukturController@jamSelesai');

    Route::get('laporan_gym', 'App\Http\Controllers\LaporanController@laporanGym');
    Route::get('laporan_kelas', 'App\Http\Controllers\LaporanController@laporanPaketKelas');
    Route::get('laporan_pendapatan', 'App\Http\Controllers\LaporanController@laporanPendapatan');
    Route::get('laporan_instruktur', 'App\Http\Controllers\LaporanController@laporanInstruktur');

