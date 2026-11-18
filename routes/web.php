<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard.index');
});

Route::get('/pelanggan', function () {
    return view('dashboard.pelanggan');
});

Route::get('/kendaraan', function () {
    return view('dashboard.kendaraan');
});

Route::get('/daftar-service', function () {
    return view('dashboard.daftar-service');
});

Route::get('/data-service', function () {
    return view('dashboard.data-service');
});

Route::get('/pembayaran-service', function () {
    return view('dashboard.pembayaran-service');
});