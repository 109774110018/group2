<?php
require_once 'config.php';
require_once 'auth_check.php';

$success = $error = '';

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $check = mysqli_query($conn, "SELECT id FROM sales WHERE product_id=$id LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $error = "Cannot delete: product has existing sales records.";
    } else {
        $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) $success = "Product deleted successfully.";
        else $error = "Error deleting product.";
    }
}

// CREATE
if ($_SERVER['REQUEST_METHOD']==='POST' && $_POST['action']==='create') {
    $name  = trim($_POST['product_name']);
    $cat   = trim($_POST['category']);
    $price = (float)$_POST['unit_price'];
    $stock = (int)$_POST['stock_quantity'];
    if (!$name || !$cat || $price<=0) { $error="Fill all required fields."; }
    else {
        $stmt = mysqli_prepare($conn,"INSERT INTO products (product_name,category,unit_price,stock_quantity) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($stmt,"ssdi",$name,$cat,$price,$stock);
        if (mysqli_stmt_execute($stmt)) $success="Product added.";
        else $error="Error adding product.";
    }
}

// UPDATE
if ($_SERVER['REQUEST_METHOD']==='POST' && $_POST['action']==='update') {
    $id    = (int)$_POST['id'];
    $name  = trim($_POST['product_name']);
    $cat   = trim($_POST['category']);
    $price = (float)$_POST['unit_price'];
    $stock = (int)$_POST['stock_quantity'];
    $stmt  = mysqli_prepare($conn,"UPDATE products SET product_name=?,category=?,unit_price=?,stock_quantity=? WHERE id=?");
    mysqli_stmt_bind_param($stmt,"ssdii",$name,$cat,$price,$stock,$id);
    if (mysqli_stmt_execute($stmt)) $success="Product updated.";
    else $error="Error updating product.";
}

$edit = null;
if (isset($_GET['edit'])) {
    $res  = mysqli_query($conn,"SELECT * FROM products WHERE id=".(int)$_GET['edit']);
    $edit = mysqli_fetch_assoc($res);
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where  = $search ? "WHERE product_name LIKE '%".mysqli_real_escape_string($conn,$search)."%' OR category LIKE '%".mysqli_real_escape_string($conn,$search)."%'" : '';
$products = mysqli_query($conn,"SELECT * FROM products $where ORDER BY product_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Products – SalesTrack</title>
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
            <h4 class="fw-bold mb-0"><i class="bi bi-box-seam me-2" style="color:#166534;"></i>Products</h4>
            <small class="text-muted">Manage product catalog and stock</small>
        </div>
        <?php if (isset($_GET['edit'])): ?>
            <a href="products.php" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Add Product
            </a>
        <?php else: ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                <i class="bi bi-plus-lg me-1"></i>Add Product
            </button>
        <?php endif; ?>
    </div>

    <?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i><?= $success ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-circle me-1"></i><?= $error ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search product or category..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Search</button>
                    <a href="products.php" class="btn btn-outline-secondary ms-1"><i class="bi bi-x"></i> Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f0fdf4;">
                        <tr>
                            <th class="px-3">ID</th><th>Product Name</th><th>Category</th>
                            <th>Unit Price</th><th>Stock</th><th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($products)===0): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No products found.</td></tr>
                    <?php else: while ($row=mysqli_fetch_assoc($products)): ?>
                        <tr>
                            <td class="px-3 text-muted">#<?= $row['id'] ?></td>
                            <td class="fw-600"><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['category']) ?></span></td>
                            <td>₱<?= number_format($row['unit_price'],2) ?></td>
                            <td>
                                <?php $cls = $row['stock_quantity']<=5?'danger':($row['stock_quantity']<=15?'warning':'success'); ?>
                                <span class="badge bg-<?= $cls ?>"><?= $row['stock_quantity'] ?> units</span>
                            </td>
                            <td class="text-center">
                                <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this product?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#f0fdf4;border-bottom:1px solid #bbf7d0;">
                <h5 class="modal-title fw-bold"><?= $edit?'Edit Product':'Add Product' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="products.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="<?= $edit?'update':'create' ?>">
                    <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label fw-600">Product Name *</label>
                        <input type="text" name="product_name" class="form-control" value="<?= $edit?htmlspecialchars($edit['product_name']):'' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Category *</label>
                        <input type="text" name="category" class="form-control" value="<?= $edit?htmlspecialchars($edit['category']):'' ?>" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600">Unit Price (₱) *</label>
                            <input type="number" name="unit_price" class="form-control" step="0.01" min="0" value="<?= $edit?$edit['unit_price']:'' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Stock Quantity</label>
                            <input type="number" name="stock_quantity" class="form-control" min="0" value="<?= $edit?$edit['stock_quantity']:0 ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i><?= $edit?'Update':'Save' ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php if ($edit): ?><script>window.addEventListener('DOMContentLoaded',function(){new bootstrap.Modal(document.getElementById('productModal')).show();});</script><?php endif; ?>
</body>
</html>