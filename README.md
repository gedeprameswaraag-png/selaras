# Selaras — Student Productivity & Wellbeing

**Selaras** adalah aplikasi berbasis web yang dirancang untuk membantu mahasiswa mengelola aktivitas sehari-hari sekaligus memperhatikan kondisi stres dan kesejahteraan diri.

Melalui fitur pengingat, pengelolaan jadwal, dan check-in harian, Selaras membantu pengguna memahami beban aktivitas mereka, menjaga keseimbangan antara produktivitas dan istirahat, serta membangun kebiasaan yang lebih teratur.

## ✨ Fitur Utama

- **Task Management** — Membuat dan mengelola daftar tugas agar aktivitas lebih terorganisir.
- **Schedule & Reminders** — Mengatur jadwal kegiatan dan membantu pengguna mengingat aktivitas yang perlu dilakukan.
- **Daily Stress Check-in** — Mencatat kondisi harian, termasuk tingkat stres dan suasana hati, untuk membantu pengguna mengenali pola kesehariannya.
- **Smart Scheduling** — Membantu menyusun aktivitas dengan mempertimbangkan beban kegiatan dan kebutuhan istirahat.
- **Recommendations** — Memberikan saran berdasarkan data aktivitas dan check-in pengguna.
- **Insights** — Menampilkan ringkasan dan gambaran pola aktivitas serta kondisi harian.
- **User Authentication** — Mendukung registrasi, login, dan verifikasi email.

## 🎯 Tujuan Proyek

Selaras dikembangkan untuk membantu mahasiswa yang sering menghadapi jadwal padat, kesulitan mengatur prioritas, lupa terhadap tugas, atau merasa kewalahan oleh banyaknya aktivitas.

Aplikasi ini bertujuan mendorong pengelolaan waktu yang lebih baik tanpa mengabaikan kebutuhan istirahat dan kesejahteraan diri.

**Selaras bukan alat diagnosis medis.** Pencatatan stres dan rekomendasi yang tersedia ditujukan untuk refleksi diri, bukan pengganti konsultasi dengan tenaga profesional.

## 🛠️ Teknologi

- **Backend:** PHP, Laravel
- **Frontend:** Blade, JavaScript, Tailwind CSS
- **Database:** MySQL
- **Build Tools:** Vite, npm
- **Version Control:** Git & GitHub

## 🚀 Instalasi dan Menjalankan Proyek

### Persyaratan

- PHP dan Composer
- MySQL
- Node.js dan npm

### 1. Clone repository

```bash
git clone https://github.com/gedeprameswaraag-png/selaras.git
cd selaras
```

### 2. Instal dependensi

```bash
composer install
npm install
```

### 3. Siapkan konfigurasi lingkungan

```bash
cp .env.example .env
php artisan key:generate
```

Pada Windows PowerShell, gunakan `Copy-Item .env.example .env` jika perintah `cp` tidak tersedia.

Sesuaikan konfigurasi database dan email di `.env` sesuai lingkungan lokalmu.

### 4. Siapkan database

Buat database MySQL untuk Selaras, lalu sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada `.env`.

Jalankan migrasi:

```bash
php artisan migrate
