<?php
define('DB_HOST', 'sql100.infinityfree.com');
define('DB_USER', 'if0_41551668');
define('DB_PASS', '9RD7BlXGdNNLXn');
define('DB_NAME', 'if0_41551668_db_sales');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();
?>
