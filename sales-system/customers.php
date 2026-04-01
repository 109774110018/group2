<?php
require_once 'config.php';
require_once 'auth_check.php';

$success = $error = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $check = mysqli_query($conn,"SELECT id FROM sales WHERE customer_id=$id LIMIT 1");
    if (mysqli_num_rows($check)>0) $error="Cannot delete: customer has existing sales records.";
    else {
        $stmt = mysqli_prepare($conn,"DELETE FROM customers WHERE id=?");
        mysqli_stmt_bind_param($stmt,"i",$id);
        if (mysqli_stmt_execute($stmt)) $success="Customer deleted.";
        else $error="Error deleting customer.";
    }
}

if ($_SERVER['REQUEST_METHOD']==='POST' && $_POST['action']==='create') {
    $name  = trim($_POST['customer_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $addr  = trim($_POST['address']);
    if (!$name) { $error="Customer name is required."; }
    else {
        $stmt = mysqli_prepare($conn,"INSERT INTO customers (customer_name,email,phone,address) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($stmt,"ssss",$name,$email,$phone,$addr);
        if (mysqli_stmt_execute($stmt)) $success="Customer added.";
        else $error="Error adding customer.";
    }
}

if ($_SERVER['REQUEST_METHOD']==='POST' && $_POST['action']==='update') {
    $id    = (int)$_POST['id'];
    $name  = trim($_POST['customer_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $addr  = trim($_POST['address']);
    $stmt  = mysqli_prepare($conn,"UPDATE customers SET customer_name=?,email=?,phone=?,address=? WHERE id=?");
    mysqli_stmt_bind_param($stmt,"ssssi",$name,$email,$phone,$addr,$id);
    if (mysqli_stmt_execute($stmt)) $success="Customer updated.";
    else $error="Error updating.";
}

$edit = null;
if (isset($_GET['edit'])) {
    $res  = mysqli_query($conn,"SELECT * FROM customers WHERE id=".(int)$_GET['edit']);
    $edit = mysqli_fetch_assoc($res);
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where  = $search ? "WHERE customer_name LIKE '%".mysqli_real_escape_string($conn,$search)."%' OR email LIKE '%".mysqli_real_escape_string($conn,$search)."%'" : '';
$customers = mysqli_query($conn,"SELECT * FROM customers $where ORDER BY customer_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Customers – SalesTrack</title>
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
            <h4 class="fw-bold mb-0"><i class="bi bi-people me-2" style="color:#166534;"></i>Customers</h4>
            <small class="text-muted">Manage customer records</small>
        </div>
        <?php if (isset($_GET['edit'])): ?>
            <a href="customers.php" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Add Customer
            </a>
        <?php else: ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#custModal">
                <i class="bi bi-plus-lg me-1"></i>Add Customer
            </button>
        <?php endif; ?>
    </div>

    <?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i><?= $success ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-circle me-1"></i><?= $error ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body py-2">
            <form method="GET" class="row g-2">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search name or email..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="customers.php" class="btn btn-outline-secondary ms-1">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f0fdf4;">
                        <tr><th class="px-3">ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th class="text-center">Actions</th></tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($customers)===0): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No customers found.</td></tr>
                    <?php else: while ($row=mysqli_fetch_assoc($customers)): ?>
                        <tr>
                            <td class="px-3 text-muted">#<?= $row['id'] ?></td>
                            <td class="fw-600"><?= htmlspecialchars($row['customer_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td class="text-truncate" style="max-width:180px"><?= htmlspecialchars($row['address']) ?></td>
                            <td class="text-center">
                                <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this customer?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="custModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#f0fdf4;border-bottom:1px solid #bbf7d0;">
                <h5 class="modal-title fw-bold"><?= $edit?'Edit Customer':'Add Customer' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="customers.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="<?= $edit?'update':'create' ?>">
                    <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label fw-600">Full Name *</label>
                        <input type="text" name="customer_name" class="form-control" value="<?= $edit?htmlspecialchars($edit['customer_name']):'' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= $edit?htmlspecialchars($edit['email']):'' ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= $edit?htmlspecialchars($edit['phone']):'' ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Address</label>
                        <textarea name="address" class="form-control" rows="2"><?= $edit?htmlspecialchars($edit['address']):'' ?></textarea>
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

<?php if ($edit): ?><script>window.addEventListener('DOMContentLoaded',function(){new bootstrap.Modal(document.getElementById('custModal')).show();});</script><?php endif; ?>
</body>
</html>