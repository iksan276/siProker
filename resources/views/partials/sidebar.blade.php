<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-university"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Admin Panel</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">
    @if(auth()->user()->isAdmin())
        <!-- Nav Item - Dashboard -->
        <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">
    @endif
    <!-- Heading -->
    <div class="sidebar-heading">
        Management
    </div>

    @if(auth()->user()->isAdmin())
    <!-- Nav Item - Users - Only for Admin -->
    <li class="nav-item {{ request()->is('users*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('users.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Users</span>
        </a>
    </li>

    <!-- Nav Item - Renstra - Only for Admin -->
    <li class="nav-item {{ request()->is('renstras*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('renstras.index') }}">
            <i class="fas fa-fw fa-book"></i>
            <span>Renstra</span>
        </a>
    </li>
    @endif

    <!-- Nav Item - Pilar - For All Users -->
    <li class="nav-item {{ request()->is('pilars*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('pilars.index') }}">
            <i class="fas fa-fw fa-columns"></i>
            <span>Pilar</span>
        </a>
    </li>

    @if(auth()->user()->isAdmin())
    <!-- Nav Item - Isu Strategis - Only for Admin -->
    <li class="nav-item {{ request()->is('isu-strategis*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('isu-strategis.index') }}">
            <i class="fas fa-fw fa-lightbulb"></i>
            <span>Isu Strategis</span>
        </a>
    </li>

     <!-- Nav Item - Jenis Kegiatan - Only for Admin -->
     <li class="nav-item {{ request()->is('jenis-kegiatans*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('jenis-kegiatans.index') }}">
            <i class="fas fa-fw fa-list-alt"></i>
            <span>Jenis Kegiatan</span>
        </a>
    </li>

    <!-- Nav Item - Satuan - Only for Admin -->
    <li class="nav-item {{ request()->is('satuans*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('satuans.index') }}">
            <i class="fas fa-fw fa-ruler"></i>
            <span>Satuan</span>
        </a>
    </li>

    <!-- Nav Item - Unit - Only for Admin -->
    <li class="nav-item {{ request()->is('units*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('units.index') }}">
            <i class="fas fa-fw fa-building"></i>
            <span>Unit</span>
        </a>
    </li>

    <!-- Nav Item - Mata Anggaran - Only for Admin -->
    <li class="nav-item {{ request()->is('meta-anggarans*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('meta-anggarans.index') }}">
            <i class="fas fa-fw fa-money-bill"></i>
            <span>Mata Anggaran</span>
        </a>
    </li>

    <!-- Nav Item - Indikator Kinerja - Only for Admin -->
    <li class="nav-item {{ request()->is('indikator-kinerjas*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('indikator-kinerjas.index') }}">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>Indikator Kinerja</span>
        </a>
    </li>

       <!-- Nav Item - Program Pengembangan - Only for Admin -->
       <li class="nav-item {{ request()->is('program-pengembangans*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('program-pengembangans.index') }}">
            <i class="fas fa-fw fa-project-diagram"></i>
            <span>Program Pengembangan</span>
        </a>
    </li>


    <!-- Nav Item - Program Rektor - Only for Admin -->
    <li class="nav-item {{ request()->is('program-rektors*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('program-rektors.index') }}">
            <i class="fas fa-fw fa-tasks"></i>
            <span>Program Rektor</span>
        </a>
    </li>

    <!-- Nav Item - Kegiatan - Only for Admin -->
    <li class="nav-item {{ request()->is('kegiatans*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kegiatans.index') }}">
            <i class="fas fa-fw fa-calendar-alt"></i>
            <span>Kegiatan</span>
        </a>
    </li>

 
    <!-- 
    <div class="sidebar-heading">
        Reports
    </div>

 
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Charts</span>
        </a>
    </li>


    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-file-pdf"></i>
            <span>Reports</span>
        </a>
    </li>
    -->
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
