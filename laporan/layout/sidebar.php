<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-text mx-3">Admin</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item <?= $current_page == 'index.php' ? 'active' : '' ?>">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Kuisioner -->
    <li class="nav-item <?= $current_page == 'kuisioner.php' ? 'active' : '' ?>">
        <a class="nav-link" href="kuisioner.php">
            <i class="bi bi-file-earmark-text-fill"></i>
            <span>Kuisioner</span>
        </a>
    </li>

    <!-- Keluhan & Saran -->
    <li class="nav-item <?= $current_page == 'keluhan_saran.php' ? 'active' : '' ?>">
        <a class="nav-link" href="keluhan_saran.php">
            <i class="bi bi-chat-fill"></i>
            <span>Keluhan & Saran</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>