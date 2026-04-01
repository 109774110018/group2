<?php
require_once 'config.php';
require_once 'auth_check.php';

// Summary cards
$total_sales    = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(total_amount) AS total FROM sales WHERE payment_status='paid'"))['total'] ?? 0;
$total_orders   = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS cnt FROM sales"))['cnt'];
$pending_count  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS cnt FROM sales WHERE payment_status='pending'"))['cnt'];
$total_products = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS cnt FROM products"))['cnt'];
$total_customers= mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS cnt FROM customers"))['cnt'];

// Monthly sales (last 6 months)
$monthly = mysqli_query($conn,
    "SELECT DATE_FORMAT(sale_date,'%b %Y') AS month, SUM(total_amount) AS total
     FROM sales WHERE payment_status='paid' AND sale_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
     GROUP BY DATE_FORMAT(sale_date,'%Y-%m') ORDER BY sale_date ASC");
$months=[]; $totals=[];
while($r=mysqli_fetch_assoc($monthly)){$months[]=$r['month'];$totals[]=$r['total'];}

// Top products
$top_products = mysqli_query($conn,
    "SELECT p.product_name, SUM(s.quantity) AS total_sold, SUM(s.total_amount) AS revenue
     FROM sales s JOIN products p ON s.product_id=p.id
     GROUP BY s.product_id ORDER BY revenue DESC LIMIT 5");

// Recent sales
$recent = mysqli_query($conn,
    "SELECT s.*, c.customer_name, p.product_name FROM sales s
     JOIN customers c ON s.customer_id=c.id JOIN products p ON s.product_id=p.id
     ORDER BY s.created_at DESC LIMIT 7");

// Status breakdown
$paid      = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS c FROM sales WHERE payment_status='paid'"))['c'];
$pending   = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS c FROM sales WHERE payment_status='pending'"))['c'];
$cancelled = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS c FROM sales WHERE payment_status='cancelled'"))['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Dashboard – SalesTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-bar-chart-line me-2" style="color:#166534;"></i>Dashboard</h4>
        <small class="text-muted">Sales overview and reports</small>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="background:#dcfce7;color:#166534;width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.8rem;">Total Revenue</div>
                        <div class="fw-bold fs-5">₱<?= number_format($total_sales,0) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="background:#d1fae5;color:#065f46;width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.8rem;">Total Orders</div>
                        <div class="fw-bold fs-5"><?= $total_orders ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="background:#fef9c3;color:#d97706;width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.8rem;">Pending</div>
                        <div class="fw-bold fs-5"><?= $pending_count ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="background:#f3e8ff;color:#7c3aed;width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.8rem;">Customers</div>
                        <div class="fw-bold fs-5"><?= $total_customers ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts row -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h6 class="fw-600 mb-0"><i class="bi bi-graph-up me-1" style="color:#166534;"></i>Monthly Revenue (Paid Sales)</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h6 class="fw-600 mb-0"><i class="bi bi-pie-chart me-1" style="color:#166534;"></i>Order Status</h6>
                </div>
                <div class="card-body d-flex justify-content-center">
                    <canvas id="statusChart" height="140"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Top Products -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h6 class="fw-600 mb-0"><i class="bi bi-trophy me-1 text-warning"></i>Top Products by Revenue</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f0fdf4;"><tr><th class="px-3">Product</th><th>Sold</th><th>Revenue</th></tr></thead>
                        <tbody>
                        <?php while($r=mysqli_fetch_assoc($top_products)): ?>
                            <tr>
                                <td class="px-3 fw-600"><?= htmlspecialchars($r['product_name']) ?></td>
                                <td><?= $r['total_sold'] ?> units</td>
                                <td class="fw-600" style="color:#166534;">₱<?= number_format($r['revenue'],0) ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Sales -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-600 mb-0"><i class="bi bi-clock-history me-1" style="color:#166534;"></i>Recent Sales</h6>
                    <a href="index.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background:#f0fdf4;"><tr><th class="px-3">Customer</th><th>Product</th><th>Total</th><th>Status</th></tr></thead>
                            <tbody>
                            <?php while($r=mysqli_fetch_assoc($recent)): ?>
                                <tr>
                                    <td class="px-3"><?= htmlspecialchars($r['customer_name']) ?></td>
                                    <td><?= htmlspecialchars($r['product_name']) ?></td>
                                    <td class="fw-600">₱<?= number_format($r['total_amount'],0) ?></td>
                                    <td>
                                        <?php $b=['paid'=>'success','pending'=>'warning','cancelled'=>'danger'][$r['payment_status']]??'secondary'; ?>
                                        <span class="badge bg-<?= $b ?>"><?= ucfirst($r['payment_status']) ?></span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Monthly Chart - Forest Green
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
            label: 'Revenue (₱)',
            data: <?= json_encode($totals) ?>,
            backgroundColor: '#166534',
            borderRadius: 6
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { ticks: { callback: v => '₱'+v.toLocaleString() } } }
    }
});

// Status Doughnut
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Paid','Pending','Cancelled'],
        datasets: [{
            data: [<?= $paid ?>, <?= $pending ?>, <?= $cancelled ?>],
            backgroundColor: ['#166534','#d97706','#dc2626'],
            borderWidth: 0
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
});
</script>
</body>
</html>