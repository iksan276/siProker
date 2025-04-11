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

    <!-- Nav Item - Users -->
    <li class="nav-item {{ request()->is('users*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('users.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Users</span>
        </a>
    </li>

   

    <!-- Nav Item - Renstra -->
    <li class="nav-item {{ request()->is('renstras*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('renstras.index') }}">
            <i class="fas fa-fw fa-book"></i>
            <span>Renstra</span>
        </a>
    </li>

    <!-- Nav Item - Pilar -->
    <li class="nav-item {{ request()->is('pilars*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('pilars.index') }}">
            <i class="fas fa-fw fa-columns"></i>
            <span>Pilar</span>
        </a>
    </li>

    <!-- Nav Item - Isu Strategis -->
    <li class="nav-item {{ request()->is('isu-strategis*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('isu-strategis.index') }}">
            <i class="fas fa-fw fa-lightbulb"></i>
            <span>Isu Strategis</span>
        </a>
    </li>

    <!-- Nav Item - Program Pengembangan -->
    <li class="nav-item {{ request()->is('program-pengembangans*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('program-pengembangans.index') }}">
            <i class="fas fa-fw fa-project-diagram"></i>
            <span>Program Pengembangan</span>
        </a>
    </li>

    <!-- Nav Item - Program Rektor -->
    <li class="nav-item {{ request()->is('program-rektors*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('program-rektors.index') }}">
            <i class="fas fa-fw fa-tasks"></i>
            <span>Program Rektor</span>
        </a>
    </li>

    <!-- Nav Item - Satuan -->
    <li class="nav-item {{ request()->is('satuans*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('satuans.index') }}">
            <i class="fas fa-fw fa-ruler"></i>
            <span>Satuan</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Reports
    </div>

    <!-- Nav Item - Charts -->
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Charts</span>
        </a>
    </li>

    <!-- Nav Item - Reports -->
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-file-pdf"></i>
            <span>Reports</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
