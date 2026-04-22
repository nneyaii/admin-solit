<?php
session_start();
require_once 'config.php';
requireLogin();

// Fetch all relawan
$result = $conn->query("SELECT id, nama, email, pernah_relawan, alasan, kategori, persetujuan, status, created_at FROM gabung ORDER BY created_at DESC");
$relawan = $result->fetch_all(MYSQLI_ASSOC);

$filename = 'relawan_sobatliterasi_' . date('Ymd_His') . '.xls';

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Pragma: public');

// Build XLS (HTML table format — works in Excel)
echo '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; }
table { border-collapse: collapse; width: 100%; }
th { background: #5BB8A6; color: white; padding: 10px; border: 1px solid #ccc; font-size: 12px; }
td { padding: 8px 10px; border: 1px solid #ddd; font-size: 11px; }
tr:nth-child(even) td { background: #F0FAF8; }
.title { font-size: 16px; font-weight: bold; color: #3D9E8C; margin-bottom: 5px; }
.sub { font-size: 11px; color: #666; margin-bottom: 15px; }
</style>
</head>
<body>
<div class="title">DATA RELAWAN — SOBAT LITERASI</div>
<div class="sub">Diekspor pada: ' . date('d F Y, H:i') . ' | Total: ' . count($relawan) . ' relawan</div>
<table>
<thead>
<tr>
    <th>#</th>
    <th>Nama</th>
    <th>Email</th>
    <th>Pernah Relawan</th>
    <th>Kategori</th>
    <th>Alasan</th>
    <th>Persetujuan</th>
    <th>Status</th>
    <th>Tanggal Daftar</th>
</tr>
</thead>
<tbody>';

foreach ($relawan as $i => $r) {
    echo '<tr>
        <td>' . ($i+1) . '</td>
        <td>' . htmlspecialchars($r['nama']) . '</td>
        <td>' . htmlspecialchars($r['email']) . '</td>
        <td>' . ucfirst($r['pernah_relawan']) . '</td>
        <td>' . ucfirst($r['kategori']) . '</td>
        <td>' . htmlspecialchars($r['alasan']) . '</td>
        <td>' . ($r['persetujuan'] ? 'Ya' : 'Tidak') . '</td>
        <td>' . ucfirst($r['status']) . '</td>
        <td>' . date('d/m/Y H:i', strtotime($r['created_at'])) . '</td>
    </tr>';
}

echo '</tbody></table>
<br>
<div class="sub">— Sobat Literasi Admin Panel | Diekspor oleh: ' . htmlspecialchars($_SESSION['admin_nama']) . ' —</div>
</body></html>';
exit();
