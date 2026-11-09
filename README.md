# MotoTrust & Bayou Automotive - Sistem Bengkel Resmi & Dealer Servis Kendaraan

Aplikasi Web Lengkap Manajemen Bengkel Resmi & Dealer Servis Mobil yang dikembangkan secara komprehensif berdasarkan 3 foto referensi UI/UX.

---

## 📸 Pemetaan Fitur Sesuai 3 Foto Referensi

### 1. Dashboard Utama MotoTrust (Foto 1)
- **Tema Gelap Modern (Dark Slate & Neon Orange)** dengan tombol toggle tema satu klik.
- **4 Kartu Metrik Utama (KPI)**:
  - **Active Jobs**: 24 (`Last 30 days: +12%`)
  - **Revenue**: Rp 124.500.000 / ₹1.2L (`Last 30 days: +6%`)
  - **Customers**: 156 (`Last 30 days: +4%`)
  - **Pending Approval**: 8 (`Urgent Attention`)
- **Grafik Pendapatan Mingguan (Weekly Revenue)**: Bar chart dinamis (Senin - Minggu / Mon - Sun) berbasis HTML5 Canvas murni (offline ready).
- **Tabel SPK Terkini (Recent Jobs)**:
  - `J-101`: Rajesh Sharma (Hyundai Creta) - **In Progress**
  - `J-102`: Priya Patel (Maruti Swift) - **Completed**
  - `J-103`: Amit Verma (Honda City) - **Pending Parts**
  - `J-104`: Suresh Gupta (Toyota Innova) - **In Progress**
  - `J-105`: Deepika Rao (Ford EcoSport) - **Completed**
- **Papan Alur Kerja / Status Servis (Kanban Board)**:
  - **Pending (3)**: J-106 (Brake Issue), J-107 (Oil Change), J-108 (Engine Noise)
  - **In Progress (4)**: J-101 (Suspension), J-104 (Clutch Replacement), J-109 (AC Service), J-110 (Dent Repair)
  - **Ready for Pickup (2)**: J-102 (General Service), J-105 (Tyre Replacement)
  - *Interaktif*: Cukup klik kartu antrean untuk memindahkan status pengerjaan secara langsung!

### 2. Kalender Jadwal Booking Servis Bayou Automotive (Foto 2)
- **Tampilan Kalender Bayou**: Navigasi bulan/tahun (`< Bulan Tahun >`), tombol `Today`, switch mode tampilan `Month | Week`.
- **Slot Booking Warna-Warni**: Green, Blue, Orange, Red, Purple (John Karsley, Farhad Tarokh, Sijmen Immens, Heather Clark, Amelia Cabal, Leo Knight, Tania Perfilveya, dsb.).
- **Modal "+ Add Appointment" Presisi**:
  - Tanggal (Date) & Waktu (Time)
  - Nama Pelanggan & No. Telepon
  - Informasi Mobil / Kendaraan
  - Type of Delivery: *Customer Vehicle Pick-Up*, *Drop Off*, *Express Service*, *On-site / Home Service*
  - Toggle *Does customer need loaner?* [Yes] / [No]
  - Deskripsi Keluhan / Servis
  - Tombol **Add Appointment** (Biru khas Bayou)
- **Konversi 1-Klik ke SPK Servis**: Klik jadwal booking apa pun di kalender, lalu tekan **"Buat SPK Servis"** untuk langsung mengubah booking menjadi Surat Perintah Kerja bengkel.

### 3. Gudang Suku Cadang, Kasir & Faktur Resmi (Foto 3)
- **Gudang & Inventaris Spare Parts**:
  - Pelumas (Oli Castrol, Shell Helix, ATF transmisi)
  - Rem (Brake pad keramik, shoe, cakram rem, DOT 4 fluid)
  - Filter (Oli, udara, kabin AC)
  - Suspensi (Shock absorber depan/belakang, ball joint)
  - Aki Mobil (GS Astra MF 12V 45Ah & 60Ah)
  - Ban Radial (Bridgestone, Michelin), Busi Iridium, Kopling & Radiator Coolant
  - Peringatan Otomatis Stok Menipis (*Low Stock Alert*)
  - Tombol cepat tambah restock (+5) atau gunakan (-1)
- **Kasir & Cetak Faktur Servis Resmi**:
  - Rincian resmi jasa mekanik teknis + suku cadang
  - Subtotal, PPN (11%), dan Grand Total
  - Status pembayaran Lunas (*Paid*) / Belum Lunas (*Unpaid*)
  - Metode pembayaran kasir: QRIS, Transfer Bank, Tunai, Kartu Debit
  - Tanda tangan pelanggan dan Service Advisor (Angelina Devina)
  - Siap cetak dokumen A4 rapi dengan stylesheet print khusus.

---

## 🚀 Cara Menjalankan Aplikasi

Aplikasi ini sudah diletakkan langsung di dalam folder web server Anda di:
`c:\xampp\htdocs\Bengkel resmi dan dealer servis kendaraan`

Anda dapat menjalankannya dengan salah satu dari dua cara mudah berikut:

### Cara 1: Menggunakan Apache XAMPP (Sangat Direkomendasikan)
1. Buka **XAMPP Control Panel** di Windows.
2. Klik tombol **Start** pada modul **Apache**.
3. Buka browser (Chrome / Edge / Firefox) dan buka URL:
   ```
   http://localhost/Bengkel%20resmi%20dan%20dealer%20servis%20kendaraan/
   ```

### Cara 2: Menjalankan Server PHP Mandiri (Built-in Server)
1. Double click file `start-server.bat` di dalam folder ini, atau buka terminal dan jalankan:
   ```cmd
   C:\xampp\php\php.exe -S 127.0.0.1:8000
   ```
2. Buka browser dan kunjungi:
   ```
   http://localhost:8000
   ```

---

## 🛠️ Arsitektur & Teknologi

- **Backend**: PHP 8.1+ (Native OOP & PDO, aman, tanpa dependensi vendor eksternal yang rumit).
- **Database**: SQLite bawaan (`database/bengkel.sqlite`) yang **otomatis diinisialisasi dan diisi data sampel (auto-seeded)** saat pertama kali dijalankan. Tidak membutuhkan konfigurasi username/password database tambahan, sehingga 100% langsung jalan tanpa setup MySQL manual!
- **Frontend**: HTML5, CSS3 Modern (CSS Variables, Grid, Flexbox, Dual Theme MotoTrust Dark & Bayou Light), Vanilla JavaScript, Canvas Charts.
- **Offline Ready**: Seluruh font, ikon SVG, dan grafik Canvas berjalan 100% lokal tanpa memerlukan koneksi internet.

