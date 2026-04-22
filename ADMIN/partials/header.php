<?php
// partials/header.php — included by all admin pages
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> — Sobat Literasi Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-book-half"></i></div>
        <div>
            <div class="brand-title">Sobat Literasi</div>
            <div class="brand-sub">Admin Panel</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Menu Utama</div>
        <a href="dashboard.php" class="nav-item <?= $currentPage==='dashboard'?'active':'' ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>
        <a href="relawan.php" class="nav-item <?= $currentPage==='relawan'?'active':'' ?>">
            <i class="bi bi-people-fill"></i>
            <span>Data Relawan</span>
            <?php
            $pendingQ = $conn->query("SELECT COUNT(*) as c FROM gabung WHERE status='pending'");
            $pendingCount = $pendingQ->fetch_assoc()['c'];
            if($pendingCount > 0): ?>
            <span class="nav-badge"><?= $pendingCount ?></span>
            <?php endif; ?>
        </a>
        <a href="upload_materi.php" class="nav-item <?= $currentPage==='upload_materi'?'active':'' ?>">
            <i class="bi bi-cloud-upload-fill"></i>
            <span>Upload Materi</span>
        </a>
        <div class="nav-section-label" style="margin-top:16px">Akun</div>
        <a href="logout.php" class="nav-item nav-logout">
            <i class="bi bi-box-arrow-left"></i>
            <span>Keluar</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="admin-mini">
            <div class="admin-avatar"><?= strtoupper(substr($_SESSION['admin_nama'],0,2)) ?></div>
            <div>
                <div class="admin-mini-name"><?= htmlspecialchars($_SESSION['admin_nama']) ?></div>
                <div class="admin-mini-role">Administrator</div>
            </div>
        </div>
    </div>
</div>

<!-- OVERLAY -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- MAIN CONTENT -->
<div class="main-content" id="mainContent">
    <!-- TOPBAR -->
    <div class="topbar">
        <div class="topbar-left">
            <button class="btn-toggle" onclick="toggleSidebar()" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="page-breadcrumb">
                <span class="breadcrumb-title"><?= $pageTitle ?? 'Dashboard' ?></span>
            </div>
        </div>
        <div class="topbar-right">
            <button class="icon-btn" onclick="toggleDarkMode()" id="darkModeBtn" title="Toggle dark mode">
                <i class="bi bi-moon-fill" id="darkIcon"></i>
            </button>
            <div class="topbar-admin">
                <div class="topbar-avatar"><?= strtoupper(substr($_SESSION['admin_nama'],0,2)) ?></div>
                <div class="topbar-info">
                    <div class="topbar-name"><?= htmlspecialchars($_SESSION['admin_nama']) ?></div>
                    <div class="topbar-role">Admin</div>
                </div>
            </div>
        </div>
    </div>
    <!-- PAGE CONTENT START -->
    <div class="page-content">
