<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Username dan password tidak boleh kosong.';
    header('Location: login.php');
    exit();
}


$stmt = $conn->prepare("SELECT id, nama, username, password, email FROM admin WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_nama'] = $admin['nama'];
        $_SESSION['admin_user'] = $admin['username'];
        $_SESSION['admin_email']= $admin['email'];
        $_SESSION['login_time'] = time();
        header('Location: dashboard.php');
        exit();
    }
}

$_SESSION['login_error'] = 'Username atau password salah. Silakan coba lagi.';
header('Location: login.php');
exit();
