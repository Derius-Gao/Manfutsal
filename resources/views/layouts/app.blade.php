<!DOCTYPE html>
<html lang="en">

@php
// Helper functions
if (!function_exists('formatRupiah')) {
    function formatRupiah($amount) {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('getStatusBadge')) {
    function getStatusBadge($status) {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'confirmed' => '<span class="badge bg-success">Confirmed</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            'completed' => '<span class="badge bg-info">Completed</span>',
            'cancelled' => '<span class="badge bg-secondary">Cancelled</span>',
            'aktif' => '<span class="badge bg-success">Aktif</span>',
            'tidak_aktif' => '<span class="badge bg-danger">Tidak Aktif</span>',
            'verified' => '<span class="badge bg-success">Verified</span>',
        ];
        
        return $badges[$status] ?? '<span class="badge bg-secondary">' . ucfirst($status) . '</span>';
    }
}


if (!function_exists('getPaymentBadgeHtml')) {
    function getPaymentBadgeHtml($status) {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'verified' => '<span class="badge bg-success">Verified</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>'
        ];
        
        return $badges[$status] ?? '<span class="badge bg-secondary">' . ucfirst($status) . '</span>';
    }
}
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Dashboard')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 250px;
            background-color: #343a40;
            padding: 0;
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: #fff;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: #495057;
        }
        .sidebar .nav-link.active {
            background-color: #007bff;
        }
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            background-color: #f8f9fa;
            padding: 20px;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    @if(Auth::check())
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="pt-3">
                        <div class="text-center mb-4">
                            <h5 class="text-white">{{ config('app.name') }}</h5>
                            <small class="text-muted">{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</small>
                        </div>
                        
                        <ul class="nav flex-column">
                            <!-- Dashboard -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                                   href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                </a>
                            </li>
                            
                            <!-- Bookings -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}" 
                                   href="{{ route('bookings.index') }}">
                                    <i class="fas fa-calendar me-2"></i> 
                                    @if(Auth::user()->isCustomer())
                                        Booking Saya
                                    @elseif(Auth::user()->isManager())
                                        Konfirmasi Booking
                                    @else
                                        Semua Booking
                                    @endif
                                </a>
                            </li>
                            
                            <!-- Lapangans -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('lapangans.*') ? 'active' : '' }}" 
                                   href="{{ route('lapangans.index') }}">
                                    <i class="fas fa-map me-2"></i> 
                                    @if(Auth::user()->isCustomer())
                                        Cari Lapangan
                                    @else
                                        Kelola Lapangan
                                    @endif
                                </a>
                            </li>
                            
                            <!-- Users - hanya admin dan superadmin -->
                            @if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" 
                                       href="{{ route('users.index') }}">
                                        <i class="fas fa-users me-2"></i> Kelola User
                                    </a>
                                </li>
                            @endif
                            
                            <!-- Keuangan - manager dan admin -->
                            @if(Auth::user()->isManager() || Auth::user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('keuangan.*') ? 'active' : '' }}" 
                                       href="{{ route('keuangan.index') }}">
                                        <i class="fas fa-money-bill-wave me-2"></i> Keuangan
                                    </a>
                                </li>
                            @endif
                            
                            <!-- Settings - hanya superadmin -->
                            @if(Auth::user()->isSuperAdmin())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" 
                                       href="{{ route('settings.index') }}">
                                        <i class="fas fa-cog me-2"></i> Web Setting
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('access.*') ? 'active' : '' }}" 
                                       href="{{ route('access.index') }}">
                                        <i class="fas fa-user-shield me-2"></i> Hak Akses
                                    </a>
                                </li>
                            @endif
                            
                            <!-- Activities -->
                            <li class="nav-item mt-3">
                                <a class="nav-link {{ request()->routeIs('activities.*') ? 'active' : '' }}" 
                                   href="{{ route('activities.index') }}">
                                    <i class="fas fa-history me-2"></i> Log Activity
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('logout') }}" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
                
                <!-- Main Content -->
                <main class="main-content">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">@yield('title', 'Dashboard')</h1>
                    </div>
                    
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Silakan periksa kembali input Anda.
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        @yield('content')
    @endif

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (dibutuhkan Select2) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <!-- JavaScript Helper Functions -->
    <script>
        // Format number to Rupiah
        function formatRupiah(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }
        
        // Get status badge HTML
        function getStatusBadgeHtml(status) {
            const badges = {
                'pending': '<span class="badge bg-warning">Pending</span>',
                'confirmed': '<span class="badge bg-success">Confirmed</span>',
                'rejected': '<span class="badge bg-danger">Rejected</span>',
                'completed': '<span class="badge bg-info">Completed</span>',
                'cancelled': '<span class="badge bg-secondary">Cancelled</span>',
                'aktif': '<span class="badge bg-success">Aktif</span>',
                'tidak_aktif': '<span class="badge bg-danger">Tidak Aktif</span>',
                'verified': '<span class="badge bg-success">Verified</span>',
            };
            
            return badges[status] || '<span class="badge bg-secondary">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
        }
        
        // Get payment badge HTML
        function getPaymentBadgeHtml(status) {
            const badges = {
                'pending': '<span class="badge bg-warning">Pending</span>',
                'verified': '<span class="badge bg-success">Verified</span>',
                'rejected': '<span class="badge bg-danger">Rejected</span>'
            };
            
            return badges[status] || '<span class="badge bg-secondary">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
        }
    </script>
    
    @yield('scripts')
</body>
</html>
