<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background:#14532d;">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
            <span style="background:#166534;padding:5px 9px;border-radius:8px;font-size:1rem;border:1px solid #15803d;">
                <i class="bi bi-graph-up-arrow"></i>
            </span>
            SalesTrack
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $current=='index.php'?'active':'' ?>" href="index.php">
                        <i class="bi bi-receipt me-1"></i>Sales
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current=='products.php'?'active':'' ?>" href="products.php">
                        <i class="bi bi-box-seam me-1"></i>Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current=='customers.php'?'active':'' ?>" href="customers.php">
                        <i class="bi bi-people me-1"></i>Customers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current=='dashboard.php'?'active':'' ?>" href="dashboard.php">
                        <i class="bi bi-bar-chart-line me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current=='about-project.php'?'active':'' ?>" href="about-project.php">
                        <i class="bi bi-info-circle me-1"></i>About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current=='developers.php'?'active':'' ?>" href="developers.php">
                        <i class="bi bi-code-slash me-1"></i>Developers
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item me-2">
                    <span class="text-light" style="font-size:.85rem;opacity:.85;">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= htmlspecialchars($_SESSION['full_name']) ?>
                        <span class="badge ms-1" style="background:#166534;border:1px solid #15803d;"><?= ucfirst($_SESSION['role']) ?></span>
                    </span>
                </li>
                <li class="nav-item">
                    <button class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>


<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-body text-center py-4 px-4">
                <div style="width:60px;height:60px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="bi bi-box-arrow-right" style="font-size:1.6rem;color:#166534;"></i>
                </div>
                <h5 class="fw-bold mb-1">Log Out</h5>
                <p class="text-muted mb-4" style="font-size:.9rem;">Are you sure you want to log out of SalesTrack?</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <a href="logout.php" class="btn px-4 text-white" style="background:#166534;">
                        <i class="bi bi-box-arrow-right me-1"></i>Yes, Log Out
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>