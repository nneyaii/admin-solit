# 🌿 Sobat Literasi — Admin Dashboard

Dashboard admin modern untuk platform edukasi Sobat Literasi.
Dibuat dengan **PHP + MySQL** (pure PHP, tanpa framework), Bootstrap 5, dan Chart.js.

---

## 🚀 Cara Setup

### 1. Persyaratan
- PHP 7.4+ atau 8.x
- MySQL 5.7+ / MariaDB
- Web server: Apache (XAMPP/WAMP) atau Nginx

### 2. Database
1. Buka **phpMyAdmin** atau MySQL client
2. Import file `database.sql`:
   ```sql
   mysql -u root -p < database.sql
   ```
   Atau copy-paste isi `database.sql` ke phpMyAdmin → Tab SQL → Execute

### 3. Konfigurasi
Edit file `admin/config.php`:
```php
define('DB_HOST', 'localhost');  // host database
define('DB_USER', 'root');       // username MySQL
define('DB_PASS', '');           // password MySQL (kosongkan jika tidak ada)
define('DB_NAME', 'sobat_literasi');
```

### 4. Folder Uploads
Pastikan folder `/uploads` bisa ditulis:
```bash
chmod 755 uploads/
```

### 5. Akses
Buka browser → `http://localhost/sobat-literasi/admin/login.php`

---

## 🔐 Login Default

| Username | Password  | Nama         |
|----------|-----------|--------------|
| admin    | password  | Super Admin  |
| literasi | password  | Tim Literasi |

> ⚠️ **Segera ganti password** setelah login pertama!
> Password di database menggunakan format: `password_hash('password', PASSWORD_DEFAULT)`

---

## 📁 Struktur File

```
sobat-literasi/
├── database.sql              ← Script SQL (import ke MySQL)
├── uploads/                  ← Folder upload file PDF materi
└── admin/
    ├── config.php            ← Konfigurasi database & helpers
    ├── login.php             ← Halaman login
    ├── process_login.php     ← Handler form login
    ├── logout.php            ← Logout & destroy session
    ├── dashboard.php         ← Dashboard utama + grafik
    ├── relawan.php           ← Data relawan + search + filter
    ├── approve.php           ← AJAX: approve relawan
    ├── delete.php            ← AJAX: hapus relawan
    ├── export.php            ← Export data ke Excel (.xls)
    ├── upload_materi.php     ← Upload & kelola materi PDF
    ├── partials/
    │   ├── header.php        ← Template header + sidebar
    │   └── footer.php        ← Template footer + scripts
    └── assets/
        ├── css/
        │   └── style.css     ← Semua CSS (tosca theme)
        └── js/
            └── script.js     ← Semua JavaScript (sidebar, AJAX, chart)
```

---

## ✨ Fitur

| Fitur | Keterangan |
|-------|------------|
| 🔐 Multi Admin Login | Session-based, password_hash() |
| 📊 Dashboard | Stat cards + Bar chart + Pie chart |
| 👥 Data Relawan | Tabel lengkap + AJAX actions |
| ✅ Approve Relawan | Tanpa reload halaman (AJAX) |
| 🗑 Hapus Relawan | Animasi slide-out (AJAX) |
| 🔍 Pencarian Realtime | Debounced search + filter tanggal |
| 📤 Export Excel | Download otomatis format .xls |
| 📚 Upload Materi | PDF upload + list dengan preview |
| 🗂 Hapus Materi | AJAX + delete file fisik |
| 🌙 Dark Mode | Toggle + simpan di localStorage |
| 📱 Responsive | Mobile & desktop friendly |
| 🎨 Animasi | Fade-in, hover effects, smooth transition |

---

## 🎨 Desain

- **Warna utama**: Hijau Tosca `#5BB8A6`
- **Font**: Plus Jakarta Sans (Google Fonts)
- **Border radius**: Besar (12–24px)
- **Shadow**: Soft shadow
- **Dark mode**: Otomatis via `data-theme="dark"` di `<html>`

---

## 🛠 Teknologi

- **Backend**: PHP 8.x (Pure PHP, no framework)
- **Database**: MySQL dengan MySQLi
- **Frontend**: Bootstrap 5.3 + Custom CSS
- **Charts**: Chart.js 4.x
- **Icons**: Bootstrap Icons 1.11
- **Font**: Plus Jakarta Sans (Google Fonts)

---

## ⚙️ Kustomisasi

### Ganti warna tema
Edit di `assets/css/style.css`, cari bagian `:root`:
```css
:root {
    --tosca: #5BB8A6;        /* Warna utama */
    --tosca-dark: #3D9E8C;   /* Warna gelap */
    --tosca-light: #E8F7F4;  /* Background tosca */
}
```

### Tambah admin baru
```php
$hash = password_hash('passwordBaru', PASSWORD_DEFAULT);
// INSERT INTO admin (nama, username, password, email) VALUES ('Nama', 'username', '$hash', 'email@domain.com')
```

---

*Dibuat dengan ❤️ untuk Sobat Literasi*
