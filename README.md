# SalesInsight

**SalesInsight — Sales Data Analytics**

Aplikasi web untuk menganalisis data penjualan historis berdasarkan jumlah unit terjual. SalesInsight membantu pengguna memahami performa produk, perkembangan penjualan bulanan, serta perubahan penjualan dari waktu ke waktu melalui dashboard dan visualisasi data.

> **Understand your sales data at a glance.**

---

## Tentang Project

SalesInsight dikembangkan sebagai aplikasi analitik data penjualan, bukan sebagai sistem transaksi atau kasir.

Aplikasi berfokus pada pengolahan data historis penjualan untuk menjawab beberapa pertanyaan utama:

* Produk apa yang paling banyak terjual?
* Produk apa yang paling sedikit terjual?
* Berapa rata-rata unit terjual setiap bulan?
* Bulan apa yang memiliki penjualan tertinggi?
* Bulan apa yang memiliki penjualan terendah?
* Bagaimana perkembangan penjualan dari bulan ke bulan?
* Produk mana yang mengalami peningkatan, stabil, atau penurunan?

Saat ini aplikasi menggunakan data seeder untuk pengembangan dan pengujian. Data penjualan asli dapat diintegrasikan pada tahap pengembangan berikutnya.

---

## Fitur

### Dashboard

Menampilkan ringkasan utama data penjualan dalam satu halaman:

* Total unit terjual
* Rata-rata unit per bulan
* Produk terlaris
* Bulan dengan penjualan tertinggi
* Tren penjualan bulanan
* Produk dengan penjualan tertinggi
* Produk dengan penjualan terendah
* Insight singkat berdasarkan data

### Analisis Produk

Menampilkan performa masing-masing produk berdasarkan data historis:

* Total unit terjual
* Rata-rata unit per bulan
* Status tren produk
* Persentase perubahan
* Produk terlaris
* Produk dengan penjualan terendah
* Produk yang meningkat
* Produk yang menurun
* Perbandingan volume penjualan antarproduk

### Analisis Tren Penjualan

Menampilkan perkembangan penjualan berdasarkan periode bulanan:

* Rata-rata unit per bulan
* Penjualan tertinggi
* Penjualan terendah
* Tren penjualan bulanan
* Perubahan dari bulan sebelumnya (MoM)
* Status perubahan bulanan
* Tabel data penjualan bulanan

---

## Teknologi

SalesInsight dibangun menggunakan:

* **PHP 8.2+**
* **Laravel 12**
* **Blade**
* **Tailwind CSS v4**
* **Vite**
* **Chart.js**
* **SQLite**
* **PHPUnit**

Aplikasi menggunakan pendekatan server-rendered Laravel dan tidak menggunakan React atau Vue.

---

## Arsitektur

SalesInsight menggunakan alur sederhana:

```text
Database
    ↓
Analytics Service
    ↓
Controller
    ↓
Blade View
    ↓
Visualization
```

Struktur utama:

```text
app/
├── Http/
│   └── Controllers/
│       ├── DashboardController.php
│       ├── ProductAnalyticsController.php
│       └── TrendAnalyticsController.php
│
├── Models/
│   ├── Product.php
│   └── SalesRecord.php
│
└── Services/
    └── Analytics/
        ├── SalesMetricsService.php
        ├── ProductPerformanceService.php
        └── TrendAnalysisService.php

database/
├── migrations/
└── seeders/
    └── SalesDataSeeder.php

resources/
├── css/
├── js/
└── views/
    ├── dashboard.blade.php
    ├── products/
    │   └── index.blade.php
    ├── trends/
    │   └── index.blade.php
    └── layouts/
        └── app.blade.php

tests/
├── Feature/
└── Unit/
```

---

## Data Model

SalesInsight menggunakan dua tabel utama untuk data analitik.

### Products

```text
id
name
category
created_at
updated_at
```

### Sales Records

```text
id
product_id
sale_date
quantity
created_at
updated_at
```

Relasi:

```text
Product
   │
   └── hasMany
          │
          ▼
     SalesRecord
```

Fokus utama aplikasi adalah **quantity / unit terjual**.

SalesInsight tidak bergantung pada:

* harga
* pendapatan
* keuntungan
* margin

---

## Analisis Tren Produk

Klasifikasi tren produk menggunakan perbandingan dua bagian periode historis.

Aturan yang digunakan:

|      Perubahan | Status    |
| -------------: | --------- |
|          > +5% | Meningkat |
| -5% sampai +5% | Stabil    |
|          < -5% | Menurun   |

Untuk perubahan bulanan, aplikasi menggunakan **Month-over-Month (MoM)**:

```text
((current_month - previous_month) / previous_month) × 100
```

Perhitungan menangani kondisi khusus seperti periode pertama dan nilai periode sebelumnya sebesar nol agar tidak menghasilkan pembagian dengan nol.

---

## Menjalankan Project

### 1. Clone repository

```bash
git clone https://github.com/amrullah-dev/sales-insight.git
cd sales-insight
```

### 2. Install dependency PHP

```bash
composer install
```

### 3. Install dependency JavaScript

```bash
npm install
```

### 4. Buat file environment

```bash
cp .env.example .env
```

Untuk Windows CMD, gunakan:

```cmd
copy .env.example .env
```

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Siapkan database

SalesInsight menggunakan SQLite.

Buat file:

```text
database/database.sqlite
```

Kemudian jalankan migration dan seeder:

```bash
php artisan migrate:fresh --seed
```

Seeder akan membuat data pengembangan untuk kebutuhan pengujian dan visualisasi.

### 7. Build frontend

```bash
npm run build
```

### 8. Jalankan aplikasi

```bash
php artisan serve
```

Aplikasi dapat diakses melalui:

```text
http://127.0.0.1:8000
```

---

## Testing

Untuk menjalankan seluruh automated tests:

```bash
php artisan test
```

Test mencakup:

* Analytics Engine
* Product Performance
* Sales Metrics
* Trend Analysis
* Dashboard
* Product Analytics
* Trend Analytics
* Empty data handling
* Edge cases

---

## Development Status

| Phase             | Status            |
| ----------------- | ----------------- |
| Foundation        | ✅ Completed       |
| Analytics Engine  | ✅ Completed       |
| Localization      | ✅ Completed       |
| Dashboard         | ✅ Completed       |
| Product Analytics | ✅ Completed       |
| Trends Analytics  | 🚧 In Development |
| CSV Data Import   | ⏳ Planned         |
| Data Validation   | ⏳ Planned         |
| Final UI Polish   | ⏳ Planned         |
| Final Testing     | ⏳ Planned         |

---

## Scope

SalesInsight berfokus pada **historical sales analytics**.

### Termasuk

* Analisis data penjualan historis
* Analisis performa produk
* Analisis tren bulanan
* Visualisasi data
* Perhitungan unit terjual
* Analisis perubahan penjualan

### Tidak termasuk

* POS / kasir
* Checkout
* Pembayaran
* Invoice
* Manajemen stok
* Warehouse management
* Supplier management
* Customer management
* Revenue analysis
* Profit analysis
* Machine learning
* Forecasting
* REST API
* React/Vue SPA

---

## License

This project is developed for learning, development, and portfolio purposes.
