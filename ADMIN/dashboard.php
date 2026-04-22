<?php
session_start();
require_once 'config.php';
requireLogin();

// JSON stats endpoint for AJAX
if (isset($_GET['json']) && $_GET['json']==='stats' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $total    = $conn->query("SELECT COUNT(*) c FROM gabung")->fetch_assoc()['c'];
    $approved = $conn->query("SELECT COUNT(*) c FROM gabung WHERE status='approved'")->fetch_assoc()['c'];
    $pending  = $conn->query("SELECT COUNT(*) c FROM gabung WHERE status='pending'")->fetch_assoc()['c'];
    header('Content-Type: application/json');
    echo json_encode(compact('total','approved','pending'));
    exit();
}

// Stats
$totalRelawan    = $conn->query("SELECT COUNT(*) c FROM gabung")->fetch_assoc()['c'];
$approvedRelawan = $conn->query("SELECT COUNT(*) c FROM gabung WHERE status='approved'")->fetch_assoc()['c'];
$pendingRelawan  = $conn->query("SELECT COUNT(*) c FROM gabung WHERE status='pending'")->fetch_assoc()['c'];
$totalMateri     = $conn->query("SELECT COUNT(*) c FROM materi")->fetch_assoc()['c'];

// Chart data — relawan per day (last 14 days)
$chartData = [];
for ($i = 13; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $label = date('d M', strtotime("-$i days"));
    $r = $conn->query("SELECT COUNT(*) c FROM gabung WHERE DATE(created_at)='$date'");
    $chartData[] = ['label' => $label, 'count' => (int)$r->fetch_assoc()['c']];
}

// Recent relawan
$recentQ = $conn->query("SELECT * FROM gabung ORDER BY created_at DESC LIMIT 5");
$recentRelawan = $recentQ->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Dashboard';
include 'partials/header.php';
?>

<!-- Welcome Row -->
<div class="row mb-4 align-items-center">
    <div class="col">
        <h4 style="font-weight:800;color:var(--gray-dark);margin:0">Selamat datang kembali, <?= htmlspecialchars(explode(' ', $_SESSION['admin_nama'])[0]) ?> 👋</h4>
        <p style="color:var(--gray-mid);font-size:13px;margin-top:4px"><?= date('l, d F Y') ?> — Panel Admin Sobat Literasi</p>
    </div>
    <div class="col-auto">
        <a href="relawan.php" class="btn-tosca">
            <i class="bi bi-people-fill"></i> Data Relawan
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon tosca"><i class="bi bi-people-fill"></i></div>
            <div class="stat-num" id="stat-total" data-count="<?= $totalRelawan ?>"><?= $totalRelawan ?></div>
            <div class="stat-label">Total Relawan</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> Keseluruhan</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-patch-check-fill"></i></div>
            <div class="stat-num" id="stat-approved" data-count="<?= $approvedRelawan ?>"><?= $approvedRelawan ?></div>
            <div class="stat-label">Relawan Approved</div>
            <div class="stat-trend up"><i class="bi bi-check-circle-fill"></i> Terverifikasi</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="bi bi-clock-fill"></i></div>
            <div class="stat-num" id="stat-pending" data-count="<?= $pendingRelawan ?>"><?= $pendingRelawan ?></div>
            <div class="stat-label">Relawan Pending</div>
            <div class="stat-trend <?= $pendingRelawan>0?'down':'up' ?>"><i class="bi bi-hourglass-split"></i> Menunggu review</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-folder-fill"></i></div>
            <div class="stat-num" data-count="<?= $totalMateri ?>"><?= $totalMateri ?></div>
            <div class="stat-label">Total Materi</div>
            <div class="stat-trend up"><i class="bi bi-file-earmark-text"></i> Materi diunggah</div>
        </div>
    </div>
</div>

<!-- Chart + Recent -->
<div class="row g-3">
    <!-- Chart -->
    <div class="col-xl-8">
        <div class="card-sl" style="animation-delay:.25s">
            <div class="card-sl-header">
                <div class="card-sl-title"><i class="bi bi-bar-chart-fill"></i> Relawan per Hari (14 Hari Terakhir)</div>
            </div>
            <div class="card-sl-body">
                <div class="chart-container">
                    <canvas id="relawanChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Pie chart -->
    <div class="col-xl-4">
        <div class="card-sl" style="animation-delay:.3s">
            <div class="card-sl-header">
                <div class="card-sl-title"><i class="bi bi-pie-chart-fill"></i> Status Relawan</div>
            </div>
            <div class="card-sl-body">
                <div class="chart-container" style="height:220px">
                    <canvas id="pieChart"></canvas>
                </div>
                <div class="d-flex flex-column gap-2 mt-3">
                    <div class="d-flex justify-content-between align-items-center" style="font-size:13px">
                        <span style="display:flex;align-items:center;gap:6px"><span style="width:10px;height:10px;border-radius:50%;background:#5BB8A6;display:inline-block"></span>Approved</span>
                        <strong style="color:var(--gray-dark)"><?= $approvedRelawan ?></strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size:13px">
                        <span style="display:flex;align-items:center;gap:6px"><span style="width:10px;height:10px;border-radius:50%;background:#F6AD55;display:inline-block"></span>Pending</span>
                        <strong style="color:var(--gray-dark)"><?= $pendingRelawan ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Relawan -->
    <div class="col-12">
        <div class="card-sl" style="animation-delay:.35s">
            <div class="card-sl-header">
                <div class="card-sl-title"><i class="bi bi-clock-history"></i> Relawan Terbaru</div>
                <a href="relawan.php" class="btn-outline-sl"><i class="bi bi-arrow-right"></i> Lihat Semua</a>
            </div>
            <div class="table-wrap">
                <table class="table-sl">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentRelawan as $r): ?>
                        <tr>
                            <td><strong style="color:var(--gray-dark)"><?= htmlspecialchars($r['nama']) ?></strong></td>
                            <td style="color:var(--gray-mid)"><?= htmlspecialchars($r['email']) ?></td>
                            <td>
                                <span class="badge-sl badge-<?= $r['kategori'] ?>">
                                    <?= ucfirst($r['kategori']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if($r['status']==='approved'): ?>
                                <span class="badge-sl badge-approved"><span class="dot dot-green"></span>Approved</span>
                                <?php else: ?>
                                <span class="badge-sl badge-pending"><span class="dot dot-orange"></span>Pending</span>
                                <?php endif; ?>
                            </td>
                            <td style="color:var(--gray-mid);font-size:12px"><?= formatDate($r['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($recentRelawan)): ?>
                        <tr><td colspan="5" class="text-center" style="padding:40px;color:var(--gray-mid)">Belum ada data relawan</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Chart.js — Bar chart
const chartLabels = <?= json_encode(array_column($chartData, 'label')) ?>;
const chartCounts = <?= json_encode(array_column($chartData, 'count')) ?>;

const ctx = document.getElementById('relawanChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Relawan Mendaftar',
            data: chartCounts,
            backgroundColor: 'rgba(91,184,166,0.15)',
            borderColor: '#5BB8A6',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
            hoverBackgroundColor: 'rgba(91,184,166,0.35)',
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'white',
                titleColor: '#3D4A5C',
                bodyColor: '#5BB8A6',
                borderColor: '#E2E8F0',
                borderWidth: 1,
                padding: 12,
                cornerRadius: 10,
                callbacks: { label: ctx => ` ${ctx.parsed.y} relawan` }
            }
        },
        scales: {
            x: { grid:{display:false}, ticks:{color:'#8A9BB0',font:{size:11,family:'Plus Jakarta Sans'}} },
            y: { grid:{color:'rgba(0,0,0,0.04)'}, beginAtZero:true, ticks:{color:'#8A9BB0',stepSize:1,font:{size:11,family:'Plus Jakarta Sans'}} }
        }
    }
});

// Pie chart
const pie = document.getElementById('pieChart').getContext('2d');
new Chart(pie, {
    type: 'doughnut',
    data: {
        labels: ['Approved','Pending'],
        datasets:[{
            data: [<?= $approvedRelawan ?>, <?= $pendingRelawan ?>],
            backgroundColor:['rgba(91,184,166,0.85)','rgba(246,173,85,0.85)'],
            borderColor:['#5BB8A6','#F6AD55'],
            borderWidth:2,
            hoverOffset:8
        }]
    },
    options: {
        responsive:true, maintainAspectRatio:false, cutout:'68%',
        plugins:{
            legend:{display:false},
            tooltip:{
                backgroundColor:'white',titleColor:'#3D4A5C',bodyColor:'#5BB8A6',
                borderColor:'#E2E8F0',borderWidth:1,padding:12,cornerRadius:10
            }
        }
    }
});
</script>

<?php include 'partials/footer.php'; ?>
