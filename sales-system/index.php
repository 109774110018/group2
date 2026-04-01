<?php
require_once 'config.php';
require_once 'auth_check.php';

$success = $error = '';

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM sales WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) $success = "Sale record deleted successfully.";
    else $error = "Error deleting record.";
}

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $cid      = (int)$_POST['customer_id'];
    $pid      = (int)$_POST['product_id'];
    $qty      = (int)$_POST['quantity'];
    $price    = (float)$_POST['unit_price'];
    $date     = $_POST['sale_date'];
    $status   = $_POST['payment_status'];
    $notes    = trim($_POST['notes']);
    $total    = $qty * $price;

    if (!$cid || !$pid || $qty <= 0 || $price <= 0 || !$date) {
        $error = "Please fill all required fields correctly.";
    } else {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO sales (customer_id, product_id, quantity, unit_price, total_amount, sale_date, payment_status, notes, created_by)
             VALUES (?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "iiiddsssi", $cid, $pid, $qty, $price, $total, $date, $status, $notes, $_SESSION['user_id']);
        if (mysqli_stmt_execute($stmt)) $success = "Sale added successfully.";
        else $error = "Error adding sale: " . mysqli_error($conn);
    }
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id     = (int)$_POST['id'];
    $cid    = (int)$_POST['customer_id'];
    $pid    = (int)$_POST['product_id'];
    $qty    = (int)$_POST['quantity'];
    $price  = (float)$_POST['unit_price'];
    $date   = $_POST['sale_date'];
    $status = $_POST['payment_status'];
    $notes  = trim($_POST['notes']);
    $total  = $qty * $price;

    $stmt = mysqli_prepare($conn,
        "UPDATE sales SET customer_id=?, product_id=?, quantity=?, unit_price=?, total_amount=?, sale_date=?, payment_status=?, notes=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "iiiddsssi", $cid, $pid, $qty, $price, $total, $date, $status, $notes, $id);
    if (mysqli_stmt_execute($stmt)) $success = "Sale updated successfully.";
    else $error = "Error updating sale.";
}

// Fetch for edit
$edit_sale = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM sales WHERE id=$eid");
    $edit_sale = mysqli_fetch_assoc($res);
}

// Search & filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$where = "WHERE 1=1";
if ($search) $where .= " AND (c.customer_name LIKE '%".mysqli_real_escape_string($conn,$search)."%' OR p.product_name LIKE '%".mysqli_real_escape_string($conn,$search)."%')";
if ($filter_status) $where .= " AND s.payment_status = '".mysqli_real_escape_string($conn,$filter_status)."'";

$sales = mysqli_query($conn,
    "SELECT s.*, c.customer_name, p.product_name FROM sales s
     JOIN customers c ON s.customer_id = c.id
     JOIN products p ON s.product_id = p.id
     $where ORDER BY s.id ASC");

$customers = mysqli_query($conn, "SELECT * FROM customers ORDER BY customer_name");
$products  = mysqli_query($conn, "SELECT * FROM products ORDER BY product_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales – SalesTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-receipt me-2" style="color:#166534;"></i>Sales Records</h4>
            <small class="text-muted">Manage all sales transactions</small>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saleModal">
            <i class="bi bi-plus-lg me-1"></i>Add Sale
        </button>
    </div>

    <?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i><?= $success ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-circle me-1"></i><?= $error ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

    <!-- Search & Filter -->
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search customer or product..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="paid" <?= $filter_status=='paid'?'selected':'' ?>>Paid</option>
                        <option value="pending" <?= $filter_status=='pending'?'selected':'' ?>>Pending</option>
                        <option value="cancelled" <?= $filter_status=='cancelled'?'selected':'' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
                    <a href="index.php" class="btn btn-outline-secondary ms-1"><i class="bi bi-x"></i> Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f0fdf4;">
                        <tr>
                            <th class="px-3">ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($sales) === 0): ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">No sales records found.</td></tr>
                    <?php else: ?>
                    <?php while ($row = mysqli_fetch_assoc($sales)): ?>
                        <tr>
                            <td class="px-3 text-muted">#<?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['customer_name']) ?></td>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td>₱<?= number_format($row['unit_price'],2) ?></td>
                            <td class="fw-600">₱<?= number_format($row['total_amount'],2) ?></td>
                            <td><?= $row['sale_date'] ?></td>
                            <td>
                                <?php
                                $badge = ['paid'=>'success','pending'=>'warning','cancelled'=>'danger'];
                                $b = $badge[$row['payment_status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $b ?>"><?= ucfirst($row['payment_status']) ?></span>
                            </td>
                            <td class="text-center">
                                <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this sale record?')" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="saleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#f0fdf4;border-bottom:1px solid #bbf7d0;">
                <h5 class="modal-title fw-bold">
                    <?= $edit_sale ? '<i class="bi bi-pencil me-2"></i>Edit Sale' : '<i class="bi bi-plus-circle me-2"></i>Add New Sale' ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="<?= $edit_sale ? 'update' : 'create' ?>">
                    <?php if ($edit_sale): ?><input type="hidden" name="id" value="<?= $edit_sale['id'] ?>"><?php endif; ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600">Customer *</label>
                            <select name="customer_id" class="form-select" required>
                                <option value="">Select Customer</option>
                                <?php
                                mysqli_data_seek($customers, 0);
                                while ($c = mysqli_fetch_assoc($customers)):
                                    $sel = ($edit_sale && $edit_sale['customer_id']==$c['id']) ? 'selected' : '';
                                ?>
                                <option value="<?= $c['id'] ?>" <?= $sel ?>><?= htmlspecialchars($c['customer_name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Product *</label>
                            <select name="product_id" class="form-select" required id="productSelect">
                                <option value="">Select Product</option>
                                <?php
                                mysqli_data_seek($products, 0);
                                while ($p = mysqli_fetch_assoc($products)):
                                    $sel = ($edit_sale && $edit_sale['product_id']==$p['id']) ? 'selected' : '';
                                ?>
                                <option value="<?= $p['id'] ?>" data-price="<?= $p['unit_price'] ?>" <?= $sel ?>>
                                    <?= htmlspecialchars($p['product_name']) ?> (₱<?= number_format($p['unit_price'],2) ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600">Quantity *</label>
                            <input type="number" name="quantity" id="qtyInput" class="form-control" min="1"
                                   value="<?= $edit_sale ? $edit_sale['quantity'] : 1 ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600">Unit Price (₱) *</label>
                            <input type="number" name="unit_price" id="priceInput" class="form-control" step="0.01" min="0"
                                   value="<?= $edit_sale ? $edit_sale['unit_price'] : '' ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600">Total Amount</label>
                            <input type="text" id="totalDisplay" class="form-control" readonly placeholder="Auto-calculated" style="background:#f0fdf4;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Sale Date *</label>
                            <input type="date" name="sale_date" class="form-control"
                                   value="<?= $edit_sale ? $edit_sale['sale_date'] : date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Payment Status</label>
                            <select name="payment_status" class="form-select">
                                <option value="pending" <?= ($edit_sale && $edit_sale['payment_status']=='pending')?'selected':'' ?>>Pending</option>
                                <option value="paid" <?= ($edit_sale && $edit_sale['payment_status']=='paid')?'selected':'' ?>>Paid</option>
                                <option value="cancelled" <?= ($edit_sale && $edit_sale['payment_status']=='cancelled')?'selected':'' ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"><?= $edit_sale ? htmlspecialchars($edit_sale['notes']) : '' ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i><?= $edit_sale ? 'Update Sale' : 'Save Sale' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('productSelect').addEventListener('change', function() {
    const price = this.options[this.selectedIndex].getAttribute('data-price');
    if (price) document.getElementById('priceInput').value = parseFloat(price).toFixed(2);
    updateTotal();
});
function updateTotal() {
    const qty = parseFloat(document.getElementById('qtyInput').value) || 0;
    const price = parseFloat(document.getElementById('priceInput').value) || 0;
    document.getElementById('totalDisplay').value = '₱' + (qty * price).toLocaleString('en-PH', {minimumFractionDigits:2});
}
document.getElementById('qtyInput').addEventListener('input', updateTotal);
document.getElementById('priceInput').addEventListener('input', updateTotal);
updateTotal();
<?php if ($edit_sale): ?>
window.addEventListener('DOMContentLoaded', function() {
    new bootstrap.Modal(document.getElementById('saleModal')).show();
});
<?php endif; ?>
</script>
</body>
</html>