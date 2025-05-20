<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion toggled" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('asset/itp.png') }}" alt="Logo" style="width: 50px; height: 50px;">
        </div>
        <div class="sidebar-brand-text mx-3" style="white-space:nowrap">Admin Panel</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">
  
    
    <!-- Heading -->
    <div class="sidebar-heading">
        Management
    </div>

    @if(auth()->user()->isAdmin())
    <!-- Master Tree -->
    @php
        $masterActive = request()->is('users*') || request()->is('renstras*') || request()->is('satuans*') || 
                        request()->is('meta-anggarans*') || request()->is('jenis-kegiatans*') || request()->is('units*') || request()->is('ikupts*') || request()->is('kriteria-akreditasis*');
    @endphp
    <li class="nav-item {{ $masterActive ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster"
            aria-expanded="false" aria-controls="collapseMaster">
            <i class="fas fa-fw fa-folder"></i>
            <span>Master</span>
        </a>
        <div id="collapseMaster" class="collapse" aria-labelledby="headingMaster" style="z-index: 2" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="fas fa-fw fa-users"></i> Users
                </a>
                <a class="collapse-item {{ request()->is('renstras*') ? 'active' : '' }}" href="{{ route('renstras.index') }}">
                    <i class="fas fa-fw fa-book"></i> Renstra
                </a>
                <a class="collapse-item {{ request()->is('satuans*') ? 'active' : '' }}" href="{{ route('satuans.index') }}">
                    <i class="fas fa-fw fa-ruler"></i> Satuan
                </a>
                <a class="collapse-item {{ request()->is('meta-anggarans*') ? 'active' : '' }}" href="{{ route('meta-anggarans.index') }}">
                    <i class="fas fa-fw fa-money-bill"></i> Mata Anggaran
                </a>
                <a class="collapse-item {{ request()->is('jenis-kegiatans*') ? 'active' : '' }}" href="{{ route('jenis-kegiatans.index') }}">
                    <i class="fas fa-fw fa-list-alt"></i> Jenis Kegiatan
                </a>
                <a class="collapse-item {{ request()->is('units*') ? 'active' : '' }}" href="{{ route('units.index') }}">
                    <i class="fas fa-fw fa-building"></i> Unit
                </a>
                <a class="collapse-item {{ request()->is('ikupts*') ? 'active' : '' }}" href="{{ route('ikupts.index') }}">
                        <i class="fas fa-fw fa-chart-line"></i> IKU PT
                    </a>
                    <a class="collapse-item {{ request()->is('kriteria-akreditasis*') ? 'active' : '' }}" href="{{ route('kriteria-akreditasis.index') }}">
                        <i class="fas fa-fw fa-award"></i> Kriteria Akreditasi
                    </a>
            </div>
        </div>
    </li>

    <!-- Master Proker Tree -->
    @php
        $prokerActive = request()->is('pilars*') || request()->is('isu-strategis*') || 
                        request()->is('program-pengembangans*') || request()->is('indikator-kinerjas*') || 
                        request()->is('program-rektors*') || request()->is('kegiatans*');
    @endphp
    <li class="nav-item {{ $prokerActive ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProker"
            aria-expanded="false" aria-controls="collapseProker">
            <i class="fas fa-fw fa-project-diagram"></i>
            <span>Master Proker</span>
        </a>
        <div id="collapseProker" class="collapse" aria-labelledby="headingProker" style="z-index: 2" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('pilars*') ? 'active' : '' }}" href="{{ route('pilars.index') }}">
                    <i class="fas fa-fw fa-columns"></i> Pilar
                </a>
                <a class="collapse-item {{ request()->is('isu-strategis*') ? 'active' : '' }}" href="{{ route('isu-strategis.index') }}">
                    <i class="fas fa-fw fa-lightbulb"></i> Isu Strategis
                </a>
                <a class="collapse-item {{ request()->is('program-pengembangans*') ? 'active' : '' }}" href="{{ route('program-pengembangans.index') }}">
                    <i class="fas fa-fw fa-project-diagram"></i> Program Pengembangan
                </a>
                <a class="collapse-item {{ request()->is('indikator-kinerjas*') ? 'active' : '' }}" href="{{ route('indikator-kinerjas.index') }}">
                    <i class="fas fa-fw fa-chart-line"></i> Indikator Kinerja
                </a>
                <a class="collapse-item {{ request()->is('program-rektors*') ? 'active' : '' }}" href="{{ route('program-rektors.index') }}">
                    <i class="fas fa-fw fa-tasks"></i> Program Rektor
                </a>
                <a class="collapse-item {{ request()->is('kegiatans*') ? 'active' : '' }}" href="{{ route('kegiatans.index') }}">
                    <i class="fas fa-fw fa-calendar-alt"></i> Kegiatan
                </a>
            </div>
        </div>
    </li>
    @else
    <!-- For non-admin users, only show Pilar -->
    <li class="nav-item {{ request()->is('pilars*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('pilars.index') }}">
            <i class="fas fa-fw fa-columns"></i>
            <span>Pilar</span>
        </a>
    </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
