<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MOTORCARE')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Bootstrap JavaScript -->
    <!-- Dipindahkan ke HEAD agar tersedia sebelum script halaman -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7f3;
            color: #34443a;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #536b58;
            position: fixed;
            left: 0;
            top: 0;
            padding: 30px 20px;
            color: white;
            overflow-y: auto;
            z-index: 1000;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 45px;
            padding-left: 8px;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            background: white;
            color: #536b58;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
        }

        .brand h2 {
            font-size: 21px;
            font-weight: 700;
            margin: 0;
        }

        .menu-title {
            font-size: 11px;
            color: #d1ddd0;
            margin: 25px 0 12px 10px;
            letter-spacing: 1px;
        }

        .nav-menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-menu a {
            display: flex;
            align-items: center;
            gap: 13px;
            text-decoration: none;
            color: #e4ebe2;
            padding: 13px 14px;
            border-radius: 10px;
            font-size: 13px;
            transition: .2s;
        }

        .nav-menu a i {
            font-size: 18px;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            background: white;
            color: #536b58;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }

        /* TOPBAR */
        .topbar {
            height: 80px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 35px;
            border-bottom: 1px solid #edf0ec;
        }

        .admin {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            font-weight: 500;
        }

        .admin-icon {
            width: 40px;
            height: 40px;
            background: #edf1eb;
            color: #536b58;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        /* BOOTSTRAP CUSTOMIZATION */
        .btn-motorcare {
            background: #536b58;
            color: white;
            border: none;
        }

        .btn-motorcare:hover {
            background: #405645;
            color: white;
        }

        .card {
            border-radius: 15px !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #819477;
            box-shadow: 0 0 0 0.2rem rgba(129, 148, 119, 0.2);
        }

        /* RESPONSIVE */
        @media(max-width: 750px) {
            .sidebar {
                width: 75px;
                padding: 25px 10px;
            }

            .brand {
                justify-content: center;
                padding: 0;
            }

            .brand h2,
            .menu-title,
            .nav-menu a span {
                display: none;
            }

            .nav-menu a {
                justify-content: center;
                padding: 14px 5px;
            }

            .nav-menu a i {
                font-size: 20px;
            }

            .main-content {
                margin-left: 75px;
            }

            .topbar {
                padding: 0 15px;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="brand">
            <div class="brand-icon">
                <i class="bi bi-wrench-adjustable-circle"></i>
            </div>
            <h2>MOTORCARE</h2>
        </div>

        <p class="menu-title">MENU UTAMA</p>

        <nav class="nav-menu">

            <a href="/dashboard"
               class="{{ request()->is('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>

            <a href="/pelanggan"
               class="{{ request()->is('pelanggan') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Data Pelanggan</span>
            </a>

            <a href="/kendaraan"
               class="{{ request()->is('kendaraan') ? 'active' : '' }}">
                <i class="bi bi-bicycle"></i>
                <span>Data Kendaraan</span>
            </a>

            <a href="/daftar-service"
               class="{{ request()->is('daftar-service') ? 'active' : '' }}">
                <i class="bi bi-tools"></i>
                <span>Daftar Service</span>
            </a>

            <a href="/data-service"
               class="{{ request()->is('data-service') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                <span>Data Service</span>
            </a>

            <a href="/pembayaran-service"
               class="{{ request()->is('pembayaran-service') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>Pembayaran Service</span>
            </a>

        </nav>

    </aside>

    <!-- KONTEN UTAMA -->
    <main class="main-content">

        <header class="topbar">
            <div class="admin">
                <div class="admin-icon">
                    <i class="bi bi-person"></i>
                </div>
                <span>Admin MOTORCARE</span>
            </div>
        </header>

        @yield('content')

    </main>

    <!-- SCRIPT TAMBAHAN -->
    @stack('scripts')

</body>
</html>
