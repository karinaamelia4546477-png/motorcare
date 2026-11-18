@extends('layouts.app')

@section('title', 'Dashboard - MOTORCARE')

@section('content')

<div style="padding: 35px;">

    <h2 style="font-size: 25px;">Dashboard</h2>

    <p style="color: #89958b; margin-top: 5px;">
        Selamat datang di sistem pengelolaan MOTORCARE.
    </p>

    <div style="background: white; padding: 30px; border-radius: 15px; margin-top: 30px;">
        <h3>Halo, Selamat Datang! 👋</h3>
        <p style="color: #89958b; margin-top: 10px;">
            Kelola data pelanggan dan kendaraan dengan lebih mudah melalui MOTORCARE.
        </p>
    </div>

    <div style="display: flex; gap: 20px; margin-top: 25px; flex-wrap: wrap;">

        <div style="background: white; padding: 25px; border-radius: 15px; flex: 1; min-width: 180px;">
            <i class="bi bi-people" style="font-size: 30px; color: #718875;"></i>
            <p style="margin-top: 15px; color: #89958b;">Data Pelanggan</p>
            <h2>Kelola Pelanggan</h2>
        </div>

        <div style="background: white; padding: 25px; border-radius: 15px; flex: 1; min-width: 180px;">
            <i class="bi bi-car-front" style="font-size: 30px; color: #e66b57;"></i>
            <p style="margin-top: 15px; color: #89958b;">Data Kendaraan</p>
            <h2>Kelola Kendaraan</h2>
        </div>

    </div>

</div>

@endsection
