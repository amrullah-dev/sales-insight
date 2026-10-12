# SalesInsight — Sales Data Analytics

SalesInsight adalah aplikasi analisis data penjualan historis berbasis web yang dikembangkan menggunakan Laravel 12, Blade, Tailwind CSS v4, dan Chart.js. Aplikasi ini berfokus pada visualisasi performa produk, analisis tren bulanan, dan pengolahan data unit terjual dari file CSV.

## 🛠️ Teknologi & Stack

- **Backend Framework:** Laravel 12 (PHP 8.2+)
- **Frontend:** Laravel Blade & Tailwind CSS v4
- **Visualisasi Grafik:** Chart.js 4.5.1
- **Database:** SQLite
- **Build Tool:** Vite

## 📊 Fitur Utama

1. **Ringkasan Dasbor (`/`)**
   - KPI Utama: Total Unit Terjual, Rata-rata Bulanan, Produk Terlaris, dan Bulan Penjualan Tertinggi.
   - Grafik Tren Penjualan Bulanan (Line Chart).
   - Grafik Top 5 Produk Terlaris & Penjualan Terendah (Horizontal Bar Chart).
   - Ringkasan Insight otomatis berdasarkan data historis.

2. **Analisis Performa Produk (`/products`)**
   - Sorotan produk: Produk Tertinggi, Terendah, Pertumbuhan Terkuat, dan Penurunan Terkuat.
   - Grafik perbandingan seluruh produk secara visual.
   - Tabel performa produk lengkap dengan status tren (*Meningkat*, *Stabil*, *Menurun*).

3. **Analisis Tren Penjualan (`/trends`)**
   - Kartu statistik tren dan perubahan MoM (*Month-over-Month*).
   - Grafik garis perkembangan unit terjual dari waktu ke waktu.
   - Tabel histori bulanan interaktif dengan indikator perubahan persentase MoM.

4. **Impor Data CSV (`/import`)**
   - Pengunggahan file CSV dengan antarmuka *drag-and-drop*.
   - Fitur unduh templat CSV contoh.
   - Validasi file, format tanggal (`YYYY-MM-DD`), dan jumlah unit positif.
   - Pembuatan produk baru secara otomatis (*auto-creation*) jika nama produk belum ada di database.
   - Laporan kesalahan berbasis baris dalam Bahasa Indonesia.

## 📐 Arsitektur Sistem

Aplikasi menggunakan pola alur server-rendered bawaan Laravel:
`Route` → `Controller` → `Service Engine` → `Blade View`

### Services Utama:
- `SalesMetricsService`: Menghitung KPI dasar dan agregasi unit.
- `ProductPerformanceService`: Menghitung performa produk, pemeringkatan, dan status tren.
- `TrendAnalysisService`: Menghitung persentase perubahan MoM dan statistik histori bulanan.
- `SalesImportService`: Menangani parsing, validasi per baris, dan transaksi database saat impor CSV.

## 🚀 Instalasi & Cara Menjalankan

1. **Clone repository:**
   ```bash
   git clone [https://github.com/amrullah-dev/sales-insight.git](https://github.com/amrullah-dev/sales-insight.git)
   cd sales-insight
