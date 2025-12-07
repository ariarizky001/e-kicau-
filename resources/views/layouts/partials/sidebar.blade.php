<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('app.name', 'AdminLTE') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name ?? 'User' }}</a>
                <small class="text-muted">
                    @if(auth()->check())
                        {{ auth()->user()->role === 'super_admin' ? 'Super Admin' : 'Admin' }}
                    @endif
                </small>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @php
                    $userRole = auth()->check() ? auth()->user()->role : null;
                    $isSuperAdmin = $userRole === 'super_admin';
                    $isAdmin = $userRole === 'admin';
                @endphp

                <!-- Dashboard -->
                <li class="nav-header">DASHBOARD</li>
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                @if($isSuperAdmin)
                    <!-- Dashboard & Monitoring (Super Admin Only) -->
                    <li class="nav-item">
                        <a href="{{ route('activity-log.index') }}" class="nav-link {{ request()->routeIs('activity-log.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Activity Log</p>
                        </a>
                    </li>

                    <!-- Master Data (Super Admin Only) -->
                    <li class="nav-header">MASTER DATA</li>
                    <li class="nav-item">
                        <a href="{{ route('kelas-lomba.index') }}" class="nav-link {{ request()->routeIs('kelas-lomba.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-trophy"></i>
                            <p>Kelas Lomba</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('juri.index') }}" class="nav-link {{ request()->routeIs('juri.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-tie"></i>
                            <p>Juri</p>
                        </a>
                    </li>
                @endif

                <!-- Peserta & Burung (Both Roles) -->
                @if($isSuperAdmin)
                    <li class="nav-item">
                        <a href="{{ route('peserta.index') }}" class="nav-link {{ request()->routeIs('peserta.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Peserta & Burung</p>
                        </a>
                    </li>
                @else
                    <li class="nav-header">MASTER DATA</li>
                    <li class="nav-item">
                        <a href="{{ route('peserta.index') }}" class="nav-link {{ request()->routeIs('peserta.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Peserta & Burung</p>
                        </a>
                    </li>
                @endif

                <!-- Operasional -->
                <li class="nav-header">OPERASIONAL</li>
                <li class="nav-item">
                    <a href="{{ route('form-nominasi.index') }}" class="nav-link {{ request()->routeIs('form-nominasi.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Form Input Nominasi</p>
                    </a>
                </li>

                @if($isSuperAdmin)
                    <!-- Rekap & Laporan (Super Admin Only - dengan Export) -->
                    <li class="nav-item">
                        <a href="{{ route('rekap-laporan.index') }}" class="nav-link {{ request()->routeIs('rekap-laporan.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Rekap & Laporan</p>
                        </a>
                    </li>
                @else
                    <!-- Rekap Hasil (Admin Only - View Only) -->
                    <li class="nav-item">
                        <a href="{{ route('rekap-hasil.index') }}" class="nav-link {{ request()->routeIs('rekap-hasil.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Rekap Hasil</p>
                        </a>
                    </li>
                @endif

                <!-- Layar Nominasi (Both Roles) -->
                <li class="nav-item">
                    <a href="{{ route('layar-nominasi.index') }}" class="nav-link {{ request()->routeIs('layar-nominasi.*') ? 'active' : '' }}" target="_blank">
                        <i class="nav-icon fas fa-tv"></i>
                        <p>Layar Nominasi</p>
                    </a>
                </li>

                @if($isSuperAdmin)
                    <!-- Sistem (Super Admin Only) -->
                    <li class="nav-header">SISTEM</li>
                    <li class="nav-item">
                        <a href="{{ route('admin-management.index') }}" class="nav-link {{ request()->routeIs('admin-management.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-shield"></i>
                            <p>Admin Management</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pengaturan-event.index') }}" class="nav-link {{ request()->routeIs('pengaturan-event.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Pengaturan Event</p>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

