/* ============================================================
   Sobat Literasi Admin — script.js
   ============================================================ */

// ============================================================
// SIDEBAR TOGGLE
// ============================================================
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar) return;
    const isOpen = sidebar.classList.toggle('open');
    overlay.classList.toggle('show', isOpen);
    document.body.style.overflow = isOpen ? 'hidden' : '';
}

// Close sidebar on resize to desktop
window.addEventListener('resize', () => {
    if (window.innerWidth > 991) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar) sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('show');
        document.body.style.overflow = '';
    }
});

// ============================================================
// DARK MODE TOGGLE
// ============================================================
function toggleDarkMode() {
    const html = document.documentElement;
    const icon = document.getElementById('darkIcon');
    const isDark = html.getAttribute('data-theme') === 'dark';
    html.setAttribute('data-theme', isDark ? 'light' : 'dark');
    if (icon) icon.className = isDark ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
    localStorage.setItem('sl-theme', isDark ? 'light' : 'dark');
}

// Apply saved theme on load
(function() {
    const saved = localStorage.getItem('sl-theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);
    document.addEventListener('DOMContentLoaded', () => {
        const icon = document.getElementById('darkIcon');
        if (icon) icon.className = saved === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
    });
})();

// ============================================================
// TOAST NOTIFICATION
// ============================================================
function showToast(message, type = 'success') {
    let container = document.querySelector('.toast-container-sl');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container-sl';
        document.body.appendChild(container);
    }
    const icons = { success:'bi-check-circle-fill', error:'bi-x-circle-fill', info:'bi-info-circle-fill' };
    const colors = { success:'#5BB8A6', error:'#FC8181', info:'#63B3ED' };
    const toast = document.createElement('div');
    toast.className = `toast-sl ${type}`;
    toast.style.borderLeftColor = colors[type] || colors.success;
    toast.innerHTML = `<i class="bi ${icons[type] || icons.success}" style="color:${colors[type]||colors.success};font-size:16px;flex-shrink:0"></i><span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideInToast .3s ease reverse';
        setTimeout(() => toast.remove(), 280);
    }, 3000);
}

// ============================================================
// LOADING OVERLAY
// ============================================================
function showLoading(containerId) {
    const el = document.getElementById(containerId);
    if (el) el.classList.add('show');
}
function hideLoading(containerId) {
    const el = document.getElementById(containerId);
    if (el) el.classList.remove('show');
}

// ============================================================
// APPROVE RELAWAN (AJAX)
// ============================================================
function approveRelawan(id) {
    if (!confirm('Approve relawan ini?')) return;
    showLoading('tableLoading');
    fetch(`approve.php?id=${id}&action=approve`, {
        method:'GET',
        headers:{'X-Requested-With':'XMLHttpRequest'}
    })
    .then(r => r.json())
    .then(data => {
        hideLoading('tableLoading');
        if (data.success) {
            showToast('Relawan berhasil di-approve!', 'success');
            const badge = document.getElementById(`status-${id}`);
            const actions = document.getElementById(`actions-${id}`);
            if (badge) badge.innerHTML = '<span class="badge-sl badge-approved"><span class="dot dot-green"></span>Approved</span>';
            if (actions) actions.innerHTML = `<button class="btn-sm-delete" onclick="deleteRelawan(${id})"><i class="bi bi-trash3-fill"></i> Hapus</button>`;
            updateStatCards();
        } else {
            showToast(data.message || 'Gagal approve', 'error');
        }
    })
    .catch(() => { hideLoading('tableLoading'); showToast('Terjadi kesalahan koneksi', 'error'); });
}

// ============================================================
// DELETE RELAWAN (AJAX)
// ============================================================
function deleteRelawan(id) {
    if (!confirm('Hapus data relawan ini secara permanen?')) return;
    showLoading('tableLoading');
    fetch(`delete.php?id=${id}`, {
        method:'GET',
        headers:{'X-Requested-With':'XMLHttpRequest'}
    })
    .then(r => r.json())
    .then(data => {
        hideLoading('tableLoading');
        if (data.success) {
            showToast('Data relawan berhasil dihapus', 'success');
            const row = document.getElementById(`row-${id}`);
            if (row) {
                row.style.transition = 'all .3s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                setTimeout(() => row.remove(), 300);
            }
            updateStatCards();
        } else {
            showToast(data.message || 'Gagal hapus', 'error');
        }
    })
    .catch(() => { hideLoading('tableLoading'); showToast('Terjadi kesalahan koneksi', 'error'); });
}

// ============================================================
// UPDATE STAT CARDS (re-fetch counts)
// ============================================================
function updateStatCards() {
    fetch('dashboard.php?json=stats', { headers:{'X-Requested-With':'XMLHttpRequest'} })
    .then(r => r.json())
    .then(data => {
        if (data.total !== undefined) {
            const els = {
                'stat-total': data.total,
                'stat-approved': data.approved,
                'stat-pending': data.pending
            };
            Object.entries(els).forEach(([id, val]) => {
                const el = document.getElementById(id);
                if (el) animateNumber(el, parseInt(el.textContent)||0, val);
            });
        }
    }).catch(()=>{});
}

// Animate number counting
function animateNumber(el, from, to) {
    const duration = 600;
    const start = performance.now();
    function step(now) {
        const t = Math.min((now - start) / duration, 1);
        el.textContent = Math.round(from + (to - from) * t);
        if (t < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
}

// ============================================================
// REALTIME SEARCH (relawan table)
// ============================================================
let searchTimeout;
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    const dateFilter = document.getElementById('dateFilter');
    if (!searchInput) return;

    function doSearch() {
        const query = searchInput.value.trim();
        const date = dateFilter ? dateFilter.value : '';
        showLoading('tableLoading');

        fetch(`relawan.php?search=${encodeURIComponent(query)}&date=${encodeURIComponent(date)}&ajax=1`, {
            headers:{'X-Requested-With':'XMLHttpRequest'}
        })
        .then(r => r.text())
        .then(html => {
            hideLoading('tableLoading');
            const tbody = document.getElementById('relawanTbody');
            if (tbody) tbody.innerHTML = html;
        })
        .catch(() => hideLoading('tableLoading'));
    }

    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(doSearch, 350);
    });

    if (dateFilter) dateFilter.addEventListener('change', doSearch);
}

// ============================================================
// DELETE MATERI (AJAX)
// ============================================================
function deleteMateri(id) {
    if (!confirm('Hapus materi ini?')) return;
    fetch(`upload_materi.php?delete=${id}`, {
        headers:{'X-Requested-With':'XMLHttpRequest'}
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Materi berhasil dihapus', 'success');
            const item = document.getElementById(`materi-${id}`);
            if (item) {
                item.style.transition = 'all .3s ease';
                item.style.opacity = '0';
                setTimeout(() => item.remove(), 300);
            }
        } else {
            showToast(data.message || 'Gagal hapus materi', 'error');
        }
    })
    .catch(() => showToast('Terjadi kesalahan', 'error'));
}

// ============================================================
// FILE UPLOAD PREVIEW
// ============================================================
function initUploadZone() {
    const zone = document.getElementById('uploadZone');
    const input = document.getElementById('fileInput');
    const preview = document.getElementById('filePreview');
    if (!zone || !input) return;

    zone.addEventListener('click', () => input.click());
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor='var(--tosca)'; zone.style.background='var(--tosca-light)'; });
    zone.addEventListener('dragleave', () => { zone.style.borderColor=''; zone.style.background=''; });
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.style.borderColor=''; zone.style.background='';
        if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; updatePreview(); }
    });
    input.addEventListener('change', updatePreview);

    function updatePreview() {
        const file = input.files[0];
        if (!file) return;
        const size = file.size > 1048576 ? (file.size/1048576).toFixed(1)+' MB' : (file.size/1024).toFixed(0)+' KB';
        if (preview) {
            preview.innerHTML = `<div class="d-flex align-items-center gap-3 p-3" style="background:var(--tosca-xlight);border-radius:12px;border:1px solid var(--tosca-mid)">
                <i class="bi bi-file-earmark-pdf-fill" style="font-size:28px;color:var(--tosca-dark)"></i>
                <div><div style="font-size:13px;font-weight:700;color:var(--gray-dark)">${file.name}</div>
                <div style="font-size:12px;color:var(--gray-mid)">${size}</div></div>
                <i class="bi bi-check-circle-fill ms-auto" style="color:var(--tosca);font-size:20px"></i></div>`;
        }
        zone.style.display = 'none';
    }
}

// ============================================================
// UPLOAD FORM PROGRESS
// ============================================================
function initUploadForm() {
    const form = document.getElementById('uploadForm');
    if (!form) return;
    form.addEventListener('submit', function() {
        const btn = form.querySelector('button[type=submit]');
        if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengupload...'; }
    });
}

// ============================================================
// DOM READY
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    initSearch();
    initUploadZone();
    initUploadForm();

    // Animate stat numbers on load
    document.querySelectorAll('[data-count]').forEach(el => {
        const target = parseInt(el.getAttribute('data-count')) || 0;
        animateNumber(el, 0, target);
    });
});
