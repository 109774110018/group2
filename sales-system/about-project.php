<?php
require_once 'config.php';
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>About – SalesTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container py-5" style="max-width:860px;">
    <div class="text-center mb-5">
        <div style="width:72px;height:72px;background:#157347;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:2rem;color:white;margin:0 auto 1rem;">
            <i class="bi bi-graph-up-arrow"></i>
        </div>
        <h2 class="fw-bold">SalesTrack</h2>
        <p class="text-muted">Sales Tracking System</p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-bullseye text-primary me-2"></i>Purpose of the System</h5>
            <p>SalesTrack is a web-based Sales Tracking System designed to help small businesses and organizations efficiently record, monitor, and analyze their sales transactions. The system provides a centralized platform for managing products, customers, and sales data in real-time.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-list-check text-primary me-2"></i>System Features</h5>
            <div class="row g-3">
                <?php
                $features = [
                    ['bi-plus-circle','Create Sales','Add new sales transactions with customer, product, quantity, and price.'],
                    ['bi-table','View Records','Display all sales in a sortable, searchable table.'],
                    ['bi-pencil','Edit Sales','Update existing sale records including payment status.'],
                    ['bi-trash','Delete Sales','Remove incorrect or cancelled sales entries.'],
                    ['bi-search','Search & Filter','Search by customer/product name and filter by payment status.'],
                    ['bi-bar-chart','Dashboard','Visual reports showing revenue charts and top products.'],
                    ['bi-box-seam','Product CRUD','Manage product catalog with pricing and stock info.'],
                    ['bi-people','Customer CRUD','Maintain a complete customer database.'],
                    ['bi-shield-lock','Login System','Secure user authentication with session management.'],
                ];
                foreach($features as $f): ?>
                <div class="col-md-4">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi <?= $f[0] ?> text-primary mt-1"></i>
                        <div>
                            <div class="fw-600"><?= $f[1] ?></div>
                            <small class="text-muted"><?= $f[2] ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-tools text-primary me-2"></i>Technologies Used</h5>
            <div class="d-flex flex-wrap gap-2">
                <?php
                $tech = ['PHP 8','MySQL','phpMyAdmin','XAMPP','HTML5','CSS3','Bootstrap 5','JavaScript','Chart.js','Visual Studio Code','GitHub','InfinityFree'];
                foreach($tech as $t): ?>
                <span class="badge bg-light text-dark border px-3 py-2" style="font-size:.85rem;"><?= $t ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>