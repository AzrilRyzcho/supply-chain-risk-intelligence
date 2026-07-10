# RiskIntel - Supply Chain Risk Intelligence

[![Laravel Version](https://img.shields.io/badge/Laravel-v13.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-v8.3%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**RiskIntel** adalah platform berbasis web pintar yang dirancang untuk memantau, menganalisis, dan memitigasi risiko rantai pasok global secara real-time. Dengan memanfaatkan integrasi data makroekonomi, cuaca ekstrem, fluktuasi nilai valas, dan analisis sentimen berita logistik, RiskIntel memberikan gambaran komprehensif mengenai indeks risiko negara mitra dagang strategis Anda.

---

## 🚀 Fitur Utama

- **Weighted Composite Risk Scoring Engine**: Model penghitungan risiko terbobot dinamis (masing-masing komponen berbobot 25%):
  - **Risiko Cuaca**: Dihitung berdasarkan kecepatan angin, curah hujan, dan tingkat bahaya badai.
  - **Risiko Inflasi**: Dihitung dari deviasi inflasi tahunan terhadap standar target 2.0%.
  - **Risiko Nilai Tukar**: Volatilitas (koefisien variasi) mata uang lokal terhadap USD dalam 30 hari terakhir.
  - **Risiko Sentimen Politik/Logistik**: Analisis sentimen otomatis (Positif, Netral, Negatif) menggunakan metode leksikon terhadap berita rantai pasok.
- **Interactive Analytics Dashboard**: Visualisasi peta interaktif pelabuhan global menggunakan *Leaflet.js*, bagan tren ekonomi dengan *Chart.js*, dan daftar pantau (*Watchlist*) negara pilihan.
- **Manajemen Cache Berita**: Sistem caching database lokal untuk menghemat penggunaan kuota API eksternal dan memungkinkan pembersihan manual oleh Administrator.
- **REST API Internal Terenkripsi & Publik**: Akses data terstruktur untuk integrasi pihak ketiga, lengkap dengan halaman dokumentasi interaktif bawaan.
- **Admin Panel**: Kelola data negara, daftar pelabuhan strategis, publikasi artikel mitigasi risiko, dan pemantauan pengguna sistem.

---

## 🛠️ Panduan Instalasi

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek ini di lingkungan lokal Anda.

### Persyaratan Sistem
Sebelum memulai, pastikan perangkat Anda telah terinstal:
- **PHP >= 8.3** (dengan ekstensi `pdo`, `mbstring`, `openssl`, `xml`, `curl`, `zip`)
- **Composer** (Dependency Manager untuk PHP)
- **Node.js >= 20.x & NPM** (Package Manager untuk Javascript)
- **Basis Data**: SQLite (default), MySQL, atau PostgreSQL.
- **Git**

### Langkah 1: Kloning Repositori
Kloning repositori proyek dari GitHub ke mesin lokal Anda:
```bash
git clone https://github.com/AzrilRyzcho/supply-chain-risk-intelligence.git
cd supply-chain-risk-intelligence
```

### Langkah 2: Instal Dependensi PHP (Backend)
Jalankan Composer untuk menginstal semua dependensi Laravel:
```bash
composer install
```

### Langkah 3: Instal Dependensi JavaScript & CSS (Frontend)
Gunakan NPM untuk menginstal dependensi modul frontend:
```bash
npm install
```

### Langkah 4: Konfigurasi Environment & App Key
Salin berkas template `.env.example` menjadi `.env`, lalu buat kunci aplikasi (app key):
```bash
cp .env.example .env
php artisan key:generate
```

### Langkah 5: Migrasi & Seeding Database
Jalankan migrasi tabel-tabel basis data sekaligus mengisi data awal (*seed data*) ke dalam database:
```bash
php artisan migrate --seed
```

> [!IMPORTANT]
> Proses seeding akan membuat dua akun default untuk pengujian:
> - **Akun Administrator**:
>   - Email: `admin@gmail.com`
>   - Password: `admin123`
> - **Akun User Biasa**:
>   - Email: `user@example.com`
>   - Password: `password`

### Langkah 6: Kompilasi Aset Frontend
Lakukan build aset menggunakan Vite untuk performa optimal:
```bash
npm run build
```

### Langkah 7: Jalankan Server Pengembangan
Anda dapat menjalankan seluruh layanan pengembangan (PHP Server, Queue, Logger, dan Vite Live Reload) secara simultan menggunakan perintah kustom berikut:
```bash
composer dev
```
Atau jika ingin menjalankannya secara terpisah:
```bash
# Terminal 1: Menjalankan PHP local server (default: http://localhost:8000)
php artisan serve

# Terminal 2: Menjalankan kompilator aset Vite
npm run dev

# Terminal 3: Menjalankan queue listener untuk sinkronisasi latar belakang
php artisan queue:listen
```

---

## ⚙️ Panduan Konfigurasi

### 1. Konfigurasi Basis Data
Secara default, proyek ini menggunakan **SQLite**. Berkas basis data akan dibuat otomatis di `database/database.sqlite`.

Jika Anda ingin beralih ke **MySQL**, perbarui konfigurasi berikut pada berkas `.env` Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=username_mysql_anda
DB_PASSWORD=password_mysql_anda
```

### 2. Konfigurasi Antrean (Queue) & Driver Cache
Platform ini memproses sinkronisasi data makroekonomi World Bank dan pemrosesan latar belakang lainnya menggunakan antrean.
- **QUEUE_CONNECTION**: Diatur ke `database` agar antrean diproses secara andal melalui tabel basis data. Jalankan `php artisan queue:work` di produksi untuk memproses antrean ini.
- **CACHE_STORE** & **SESSION_DRIVER**: Diatur ke `database` secara default untuk mendukung skalabilitas horizontal.

### 3. Layanan Pihak Ketiga (External Services)
Untuk mengaktifkan pembaruan berita real-time, Anda perlu mendaftar dan mendapatkan API Key gratis dari **GNews API**.
Masukkan API Key tersebut ke berkas `.env`:
```env
GNEWS_API_KEY=masukkan_api_key_gnews_anda_di_sini
```

---

## 🔑 Environment Variables

Berikut adalah variabel lingkungan (`.env`) utama yang perlu diperhatikan:

| Variabel | Default | Deskripsi |
| :--- | :--- | :--- |
| `APP_NAME` | `Laravel` | Nama aplikasi yang ditampilkan di email & header. |
| `APP_ENV` | `local` | Lingkungan aplikasi (`local` / `production`). |
| `APP_DEBUG` | `true` | Menampilkan error detail (`true` untuk dev, `false` untuk prod). |
| `APP_URL` | `http://localhost` | URL absolut aplikasi Anda (digunakan untuk link email/asset). |
| `DB_CONNECTION` | `sqlite` | Koneksi database yang digunakan (`sqlite`, `mysql`, `pgsql`). |
| `QUEUE_CONNECTION` | `database` | Driver antrean pekerjaan logistik latar belakang. |
| `CACHE_STORE` | `database` | Penyimpanan cache untuk respons eksternal API. |
| `SESSION_DRIVER` | `database` | Penyimpanan sesi login pengguna. |
| `GNEWS_API_KEY` | *(Kosong)* | Kunci otentikasi API dari [gnews.io](https://gnews.io/). |

---

## 🔌 Daftar API yang Digunakan

### 1. API Eksternal (Integrasi Data)
- **GNews API** (`https://gnews.io/api/v4/search`): Digunakan untuk mengambil artikel berita industri rantai pasok global berdasarkan kata kunci negara terkait.
- **ExchangeRate API** (`https://open.er-api.com/v6/latest/`): Digunakan untuk memperbarui nilai tukar mata uang lokal terkini terhadap USD secara periodik.
- **Frankfurter API** (`https://api.frankfurter.app/`): Digunakan untuk mengambil data histori nilai tukar mata uang 30 hari terakhir guna kalkulasi volatilitas valas.
- **World Bank API** (`https://api.worldbank.org/v2/`): Digunakan untuk melakukan sinkronisasi otomatis indikator ekonomi negara (PDB, Inflasi, Populasi, Ekspor, dan Impor) selama 10 tahun terakhir.

### 2. REST API Internal (Untuk Pihak Ketiga)
Dokumentasi interaktif REST API internal dapat diakses langsung melalui peramban pada rute `/api/docs`.

#### API Publik (Tanpa Autentikasi)
Seluruh endpoint di bawah ini memiliki basis rute `/api` dan mengembalikan data dalam format JSON:

- **`GET /api/countries`**: Mengambil daftar negara mitra dagang strategis.
  - *Query Parameters*: `search` (Nama/ISO), `region` (Wilayah).
- **`GET /api/risk`**: Mengambil kalkulasi indeks risiko komposit terbaru dari setiap negara mitra.
  - *Query Parameters*: `search` (Nama/ISO).
- **`GET /api/news`**: Mengambil berita logistik rantai pasok global beserta skor analisis sentimen.
  - *Query Parameters*: `search` (Judul/Sumber), `sentiment` (`positive`, `neutral`, `negative`).
- **`GET /api/currency`**: Mengambil nilai tukar valas terbaru dari semua negara mitra terhadap USD.
  - *Query Parameters*: `search` (Kode valas).
- **`GET /api/ports`**: Mengambil daftar lokasi koordinat pelabuhan laut utama dunia.
  - *Query Parameters*: `search` (Nama/Kode pelabuhan), `country_id` (Filter ID negara).

#### API Privat / Terproteksi (v1 Group)
Endpoint ini diakses dengan prefix `/api/v1` dan membutuhkan autentikasi sesi pengguna (`auth` middleware) serta menerapkan caching respons (`api.cache` middleware):

- **`GET /api/v1/countries`**: Mengambil daftar negara (versi terproteksi).
- **`GET /api/v1/countries/{code}`**: Mengambil detail lengkap suatu negara berdasarkan kode ISO (contoh: `/api/v1/countries/ID`).
- **`POST /api/v1/countries/{code}/sync`**: Memaksa sinkronisasi latar belakang instan dengan World Bank API untuk negara tertentu.
- **`GET /api/v1/risk/{code}`**: Mengambil detail kalkulasi risiko komposit untuk kode negara tertentu.
- **`GET /api/v1/ports`**: Mengambil daftar pelabuhan global lengkap.
- **`GET /api/v1/news/{code}`**: Mengambil arsip berita logistik terasosiasi dengan kode negara.
- **`GET /api/v1/currency/{code}`**: Mengambil kurs mata uang spesifik negara tertentu terhadap USD.
- **`POST /api/v1/watchlist/toggle`**: Memasukkan/mengeluarkan negara dari daftar pantau pengguna.
  - *Payload*: `{ "country_id": 2 }`

---

## 🚢 Panduan Deployment

Ikuti langkah-langkah berikut untuk melakukan deployment RiskIntel di lingkungan produksi (Linux VPS / Cloud Server):

### 1. Persiapan Server & Izin Folder
Arahkan dokumen root Web Server (Nginx/Apache) ke direktori `/public` di dalam proyek ini. 
Pastikan direktori `storage` dan `bootstrap/cache` dapat ditulis oleh web server user (biasanya `www-data`):
```bash
sudo chown -R www-data:www-data /var/www/supply-chain-risk-intelligence
sudo chmod -R 775 /var/www/supply-chain-risk-intelligence/storage
sudo chmod -R 775 /var/www/supply-chain-risk-intelligence/bootstrap/cache
```

### 2. Pengaturan Berkas `.env` Produksi
Perbarui pengaturan environment produksi di berkas `.env`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://riskintel-domain-anda.com
```

### 3. Jalankan Instalasi Produksi
Instal dependensi tanpa menyertakan modul development (`--no-dev`) dan optimalkan autoloader:
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 4. Bersihkan dan Caching Konfigurasi
Untuk memaksimalkan kecepatan load aplikasi di produksi, cache seluruh konfigurasi, rute, dan tampilan Blade:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> [!WARNING]
> Kapan pun Anda melakukan perubahan pada berkas `.env` di lingkungan produksi, Anda **wajib** menjalankan kembali perintah `php artisan config:clear` diikuti oleh `php artisan config:cache`.

### 5. Jalankan Migrasi Secara Aman
Lakukan migrasi database di server produksi dengan flag `--force` untuk menghindari prompt konfirmasi:
```bash
php artisan migrate --force
```

### 6. Konfigurasi Supervisor untuk Antrean (Queue Worker)
Agar antrean sinkronisasi latar belakang berjalan secara berkelanjutan, gunakan monitor proses seperti **Supervisor** di Linux.
Buat berkas konfigurasi Supervisor (misalnya `/etc/supervisor/conf.d/riskintel-worker.conf`):
```ini
[program:riskintel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/supply-chain-risk-intelligence/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/supply-chain-risk-intelligence/storage/logs/worker.log
stopasgroup=true
killasgroup=true
```
Jalankan Supervisor untuk memulai worker:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start riskintel-worker:*
```

### 7. Konfigurasi Task Scheduler (Cron Job)
RiskIntel memiliki tugas terjadwal otomatis (seperti memperbarui kurs mata uang dan menyinkronkan data makroekonomi).
Tambahkan baris berikut ke cron job sistem server Anda (`crontab -e`):
```bash
* * * * * cd /var/www/supply-chain-risk-intelligence && php artisan schedule:run >> /dev/null 2>&1
```

### 8. Contoh Konfigurasi Server Blok Nginx
Berikut adalah contoh konfigurasi blok server Nginx (`/etc/nginx/sites-available/riskintel`):
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name riskintel-domain-anda.com;
    root /var/www/supply-chain-risk-intelligence/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah Lisensi MIT. Lihat berkas `LICENSE` untuk informasi lebih lanjut.