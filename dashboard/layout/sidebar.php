<?php 
$current_page = basename($_SERVER['PHP_SELF']); 
?>

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    
                    <div class="sb-sidenav-menu-heading">Core</div>
                    
                    <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="index.php">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        Dashboard
                    </a>

                    <div class="sb-sidenav-menu-heading">Laporan</div>

                    <a class="nav-link <?= ($current_page == 'kuisioner.php') ? 'active' : '' ?>" href="kuisioner.php">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-chart-area"></i>
                        </div>
                        Kuisioner
                    </a>

                    <a class="nav-link <?= ($current_page == 'keluhan_saran.php') ? 'active' : '' ?>" href="keluhan_saran.php">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-table"></i>
                        </div>
                        Keluhan & Saran
                    </a>

                </div>
            </div>

            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                Start Bootstrap
            </div>
        </nav>
    </div>