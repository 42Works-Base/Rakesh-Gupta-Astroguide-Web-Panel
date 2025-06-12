<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion toggled" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('dashboard')}}">
        <div class="sidebar-brand-icon">
            <!-- <i class="fas fa-sun"></i> -->
            <img src="{{ url('backend/img/side-bar-logo.png') }}" alt="Astrology Logo" width="100">
        </div>
        <div class="sidebar-brand-text mx-3" style="visibility: hidden;">Astro</div>
    </a>

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{route('dashboard')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>


    <!-- SUPER ADMIN MENU -->
    <li class="nav-item {{ Route::is('users.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('users.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Users</span>
        </a>
    </li>
    <li class="nav-item {{ Route::is('slot-management.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('slot-management.index') }}">
            <i class="fas fa-fw fa-building"></i>
            <span>Slot Management</span>
        </a>
    </li>

    <li class="nav-item {{ Route::is('appointment-management.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('appointment-management.index') }}">
            <i class="fas fa-fw fa-user"></i>
            <span>Appointment Management</span>
        </a>
    </li>
    <li class="nav-item {{ Route::is('transactions.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('transactions.index') }}">
            <i class="fas fa-fw fa-money-check-alt"></i>
            <span>Transactions</span>
        </a>
    </li>
    <li class="nav-item {{ Route::is('general-remedies.update') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('general-remedies.update') }}">
            <i class="fas fa-fw fa-building"></i>
            <span>General Remedies</span>
        </a>
    </li>












    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>