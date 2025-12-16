<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BSU Lamber Website</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e8f5e8 0%, #8dbb8dff 50%, #1f5121ff 100%);
            min-height: 100vh;
        }
        .sidebar {
            position: fixed;
            top: 0; bottom: 0; left: 0;
            width: 250px;
            background: #ffffff;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 100;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #2e7d32 !important;
            padding: 12px 25px;
            font-weight: 500;
            border-radius: 0;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #e8f5e8;
            color: #1b5e20 !important;
            padding-left: 35px;
        }
        .sidebar .nav-link i {
            width: 30px;
            font-size: 1.1rem;
        }
        .sidebar-heading {
            color: #43a047;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 15px 25px 8px;
            font-weight: 600;
        }
        .logo-img {
            height: 60px;
            filter: brightness(0) saturate(100%) invert(30%) sepia(82%) saturate(749%) hue-rotate(70deg) brightness(95%) contrast(95%);
        }
        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
        }
        .topbar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 15px 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        .btn-success, .btn-primary {
            background-color: #1f5121ff;
            border-color: #1f5121ff;
        }
        .btn-success:hover, .btn-primary:hover {
            background-color: #1f5121ff;
            border-color: #1f5121ff;
        }
        @media (max-width: 768px) {
            .sidebar { position: relative; width: 100%; height: auto; }
            .content-wrapper { margin-left: 0; }
        }
    </style>
</head>
<body>
    <!-- Sidebar Putih -->
    <div class="sidebar d-flex flex-column">
        <!-- Logo & Menu Utama -->
        <div class="text-center" style="position: sticky; top: 0; z-index: 10;">
            <img src="{{ asset('images/Logo-BSU.png') }}" alt="Logo" class="img-fluid" style="height: 80px; width: auto;">
        </div>
        <hr class="mx-4">

        <ul class="nav flex-column px-3">
            <li class="nav-item sidebar-heading">Menu Utama</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="/dashboard">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('penjualan*') ? 'active' : '' }}" href="{{ route('penjualan.index') }}">
                    <i class="fas fa-shopping-cart"></i> Penjualan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('nasabah*') ? 'active' : '' }}" href="{{ route('nasabah.index') }}">
                    <i class="fas fa-users"></i> Nasabah
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('tabungan*') ? 'active' : '' }}" href="{{ route('tabungan.index') }}">
                    <i class="fas fa-piggy-bank"></i> Tabungan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('kelola-harga*') ? 'active' : '' }}" href="{{ url('/kelola-harga') }}">
                    <i class="fas fa-tags"></i> Kelola Harga
                </a>
            </li>
        </ul>

        <!-- Keluar di paling bawah -->
        <div class="mt-auto px-3 pb-4">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="nav-link text-danger btn btn-link p-0 text-start w-100">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </button>
    </form>
</div>
    </div>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <main>
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>