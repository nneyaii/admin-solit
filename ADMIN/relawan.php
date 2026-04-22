<?php
session_start();
require_once 'config.php';
requireLogin();

// AJAX: return only tbody rows
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_GET['ajax']);
$search  = trim($_GET['search'] ?? '');
$date    = trim($_GET['date'] ?? '');

$where = '1=1';
if ($search) {
    $s = $conn->real_escape_string($search);
    $where .= " AND (nama LIKE '%$s%' OR email LIKE '%$s%' OR kategori LIKE '%$s%')";
}
if ($date) {
    $d = $conn->real_escape_string($date);
    $where .= " AND DATE(created_at)='$d'";
}

$result = $conn->query("SELECT * FROM gabung WHERE $where ORDER BY created_at DESC");
$relawan = $result->fetch_all(MYSQLI_ASSOC);

// AJAX: only return tbody
if ($isAjax && isset($_GET['ajax'])) {
    if (empty($relawan)) {
        echo '<tr><td colspan="7"><div class="empty-state"><i class="bi bi-search"></i><h5>Tidak ditemukan</h5><p>Tidak ada data yang cocok dengan pencarian</p></div></td></tr>';
    } else {
        foreach ($relawan as $r) {
            $statusBadge = $r['status']==='approved'
                ? '<span class="badge-sl badge-approved"><span class="dot dot-green"></span>Approved</span>'
                : '<span class="badge-sl badge-pending"><span class="dot dot-orange"></span>Pending</span>';
            $approveBtn = $r['status']==='pending'
                ? "<button class='btn-sm-approve' onclick='approveRelawan({$r['id']})'><i class='bi bi-check-lg'></i> Approve</button>"
                : '';
            echo "<tr id='row-{$r['id']}'>
                <td><strong style='color:var(--gray-dark)'>" . htmlspecialchars($r['nama']) . "</strong></td>
                <td style='color:var(--gray-mid)'>" . htmlspecialchars($r['email']) . "</td>
                <td>" . ucfirst($r['pernah_relawan']) . "</td>
                <td style='max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap' title='" . htmlspecialchars($r['alasan']) . "'>" . htmlspecialchars(substr($r['alasan'],0,50)) . (strlen($r['alasan'])>50?'...':'') . "</td>
                <td><span class='badge-sl badge-{$r['kategori']}'>" . ucfirst($r['kategori']) . "</span></td>
                <td id='status-{$r['id']}'>$statusBadge</td>
                <td id='actions-{$r['id']}' style='white-space:nowrap;display:flex;gap:6px;flex-wrap:wrap'>
                    $approveBtn
                    <button class='btn-sm-delete' onclick='deleteRelawan({$r['id']})'><i class='bi bi-trash3-fill'></i> Hapus</button>
                </td>
            </tr>";
        }
    }
    exit();
}

$pageTitle = 'Data Relawan';
include 'partials/header.php';
?>

<div class="row mb-4 align-items-center">
    <div class="col">
        <h4 style="font-weight:800;color:var(--gray-dark);margin:0">Data Relawan</h4>
        <p style="color:var(--gray-mid);font-size:13px;margin-top:4px">Kelola semua pendaftar relawan Sobat Literasi</p>
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="export.php" class="btn-tosca">
            <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
        </a>
    </div>
</div>

<div class="card-sl">
    <div class="card-sl-header">
        <div class="card-sl-title"><i class="bi bi-people-fill"></i> Daftar Relawan (<?= count($relawan) ?>)</div>
        <div class="d-flex gap-2 flex-wrap">
            <!-- Date filter -->
            <div style="position:relative">
                <i class="bi bi-calendar3" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--gray-mid);font-size:13px;z-index:1"></i>
                <input type="date" id="dateFilter" class="search-input" style="padding-left:36px;min-width:160px">
            </div>
            <!-- Search -->
            <div class="search-bar">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Cari nama, email...">
            </div>
        </div>
    </div>

    <div style="position:relative">
        <!-- Loading overlay -->
        <div class="loading-overlay" id="tableLoading">
            <div class="spinner-tosca"></div>
        </div>

        <div class="table-wrap">
            <table class="table-sl">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Pernah Relawan</th>
                        <th>Alasan</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="relawanTbody">
                    <?php if (empty($relawan)): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="bi bi-people"></i>
                                <h5>Belum ada data</h5>
                                <p>Belum ada relawan yang mendaftar</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($relawan as $r): ?>
                    <tr id="row-<?= $r['id'] ?>">
                        <td><strong style="color:var(--gray-dark)"><?= htmlspecialchars($r['nama']) ?></strong></td>
                        <td style="color:var(--gray-mid)"><?= htmlspecialchars($r['email']) ?></td>
                        <td><?= ucfirst($r['pernah_relawan']) ?></td>
                        <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= htmlspecialchars($r['alasan']) ?>">
                            <?= htmlspecialchars(substr($r['alasan'],0,50)) . (strlen($r['alasan'])>50?'...':'') ?>
                        </td>
                        <td><span class="badge-sl badge-<?= $r['kategori'] ?>"><?= ucfirst($r['kategori']) ?></span></td>
                        <td id="status-<?= $r['id'] ?>">
                            <?php if($r['status']==='approved'): ?>
                            <span class="badge-sl badge-approved"><span class="dot dot-green"></span>Approved</span>
                            <?php else: ?>
                            <span class="badge-sl badge-pending"><span class="dot dot-orange"></span>Pending</span>
                            <?php endif; ?>
                        </td>
                        <td id="actions-<?= $r['id'] ?>" style="white-space:nowrap;display:flex;gap:6px;flex-wrap:wrap">
                            <?php if($r['status']==='pending'): ?>
                            <button class="btn-sm-approve" onclick="approveRelawan(<?= $r['id'] ?>)">
                                <i class="bi bi-check-lg"></i> Approve
                            </button>
                            <?php endif; ?>
                            <button class="btn-sm-delete" onclick="deleteRelawan(<?= $r['id'] ?>)">
                                <i class="bi bi-trash3-fill"></i> Hapus
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
