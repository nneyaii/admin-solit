-- ============================================================
-- DATABASE: sobat_literasi
-- Created for: Admin Dashboard Sobat Literasi
-- ============================================================

CREATE DATABASE IF NOT EXISTS sobat_literasi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sobat_literasi;

-- ============================================================
-- TABLE: admin
-- ============================================================
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin: username=admin, password=admin123
INSERT INTO admin (nama, username, password, email) VALUES
('Super Admin', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@sobatliterasi.id'),
('Tim Literasi', 'literasi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'literasi@sobatliterasi.id');

-- ============================================================
-- TABLE: gabung (data relawan)
-- ============================================================
CREATE TABLE IF NOT EXISTS gabung (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    pernah_relawan ENUM('ya','tidak') DEFAULT 'tidak',
    alasan TEXT,
    kategori ENUM('pelajar','guru','umum') DEFAULT 'umum',
    persetujuan TINYINT(1) DEFAULT 0,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Sample data relawan
INSERT INTO gabung (nama, email, pernah_relawan, alasan, kategori, persetujuan, status, created_at) VALUES
('Anisa Putri', 'anisa@email.com', 'tidak', 'Ingin berkontribusi untuk pendidikan anak-anak', 'pelajar', 1, 'approved', '2025-01-05 10:00:00'),
('Budi Santoso', 'budi@email.com', 'ya', 'Sudah berpengalaman mengajar dan ingin terus membantu', 'guru', 1, 'approved', '2025-01-08 11:30:00'),
('Citra Dewi', 'citra@email.com', 'tidak', 'Tertarik dengan gerakan literasi nasional', 'umum', 1, 'pending', '2025-01-10 09:15:00'),
('Dani Pratama', 'dani@email.com', 'ya', 'Punya passion di bidang pendidikan', 'pelajar', 1, 'pending', '2025-01-12 14:00:00'),
('Eka Wijaya', 'eka@email.com', 'tidak', 'Ingin memberikan dampak positif di masyarakat', 'umum', 1, 'approved', '2025-01-15 08:45:00'),
('Farah Nadia', 'farah@email.com', 'ya', 'Relawan senior yang ingin kembali aktif', 'guru', 1, 'pending', '2025-01-18 16:20:00'),
('Gilang Ramadhan', 'gilang@email.com', 'tidak', 'Mahasiswa yang ingin mengabdi', 'pelajar', 1, 'approved', '2025-01-20 13:10:00'),
('Hana Maharani', 'hana@email.com', 'tidak', 'Ingin belajar sambil mengajar', 'pelajar', 1, 'pending', '2025-01-22 10:30:00'),
('Irfan Hakim', 'irfan@email.com', 'ya', 'Memiliki banyak waktu luang untuk kegiatan sosial', 'umum', 1, 'approved', '2025-01-25 12:00:00'),
('Jasmine Aulia', 'jasmine@email.com', 'tidak', 'Peduli dengan pendidikan daerah terpencil', 'guru', 1, 'pending', '2025-01-28 09:00:00');

-- ============================================================
-- TABLE: materi
-- ============================================================
CREATE TABLE IF NOT EXISTS materi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    kategori ENUM('kelas10','kelas11','kelas12') NOT NULL,
    deskripsi TEXT,
    file VARCHAR(255) NOT NULL,
    ukuran VARCHAR(50) DEFAULT NULL,
    admin_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Sample data materi
INSERT INTO materi (judul, kategori, deskripsi, file, ukuran, admin_id, created_at) VALUES
('Pengantar Bahasa Indonesia Kelas 10', 'kelas10', 'Materi dasar Bahasa Indonesia semester 1', 'materi_001.pdf', '2.3 MB', 1, '2025-01-10 10:00:00'),
('Matematika Dasar - Aljabar', 'kelas10', 'Konsep dasar aljabar dan persamaan linear', 'materi_002.pdf', '3.1 MB', 1, '2025-01-12 11:00:00'),
('Fisika Kelas 11 - Gerak Lurus', 'kelas11', 'Kinematika gerak lurus beraturan dan GLBB', 'materi_003.pdf', '4.2 MB', 2, '2025-01-15 09:30:00'),
('Kimia Organik Kelas 12', 'kelas12', 'Senyawa karbon dan hidrokarbon', 'materi_004.pdf', '5.0 MB', 1, '2025-01-18 14:00:00');
