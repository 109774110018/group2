<?php
require_once 'config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – SalesTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #166534;
            --primary-hover: #14532d;
        }
        body {
            font-family: 'Space Grotesk', sans-serif;
            background: linear-gradient(135deg, #14532d 0%, #166534 50%, #15803d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        }
        .brand-icon {
            width: 56px; height: 56px;
            background: var(--primary);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: white; margin: 0 auto 1rem;
        }
        .btn-login {
            background: var(--primary);
            color: white;
            border: none;
            padding: .75rem;
            font-weight: 600;
            border-radius: 8px;
            width: 100%;
            font-size: 1rem;
            transition: background .2s;
        }
        .btn-login:hover { background: var(--primary-hover); color: white; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 .2rem rgba(22,101,52,.15); }
        .hint { font-size: .8rem; color: #6b7280; margin-top: .5rem; }
        .password-wrapper { position: relative; }
        .password-wrapper .form-control { padding-right: 2.8rem; }
        .toggle-password {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #6b7280;
            padding: 0;
            font-size: 1.1rem;
            line-height: 1;
        }
        .toggle-password:hover { color: var(--primary); }
    </style>
</head>
<body>
<div class="login-card">
    <div class="brand-icon"><i class="bi bi-graph-up-arrow"></i></div>
    <h4 class="text-center fw-700 mb-1" style="font-weight:700;">SalesTrack</h4>
    <p class="text-center text-muted mb-4" style="font-size:.9rem;">Sales Tracking System</p>

    <?php if ($error): ?>
    <div class="alert alert-danger py-2"><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label fw-600">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Enter username"
                   value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required>
        </div>
        <div class="mb-4">
            <label class="form-label fw-600">Password</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Enter password" required>
                <button type="button" class="toggle-password" onclick="togglePassword()">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>
        <button type="submit" class="btn-login"><i class="bi bi-box-arrow-in-right me-1"></i>Login</button>
    </form>

</div>
<script>
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>
</body>
</html>