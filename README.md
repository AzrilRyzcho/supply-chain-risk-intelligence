# RiskIntel - Supply Chain Risk Intelligence

Platform berbasis Laravel untuk memantau dan memitigasi risiko rantai pasok global secara real-time melalui integrasi data cuaca, valas, inflasi, sentimen berita, dan rute pelayaran laut.

## Fitur Utama

- **Dashboard Analytic & Risk Scoring**: Pemetaan indeks risiko terbobot (cuaca, inflasi, kurs valas, dan sentimen berita logistik).
- **Live Vessel Tracking**: Visualisasi rute pelayaran antar-pelabuhan laut dunia secara interaktif berbasis Leaflet.js.
- **Daftar Pantau (Watchlist)**: Pemantauan indikator risiko untuk negara mitra dagang pilihan.
- **REST API**: Endpoint JSON untuk data risiko, berita, pelabuhan, dan mata uang.

## Cara Instalasi

1. **Clone repositori**:
   ```bash
   git clone https://github.com/AzrilRyzcho/supply-chain-risk-intelligence.git
   cd supply-chain-risk-intelligence
   ```

2. **Install dependensi**:
   ```bash
   composer install
   npm install
   ```

3. **Setup environment & database**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   ```

4. **Build aset frontend**:
   ```bash
   npm run build
   ```

5. **Jalankan server lokal**:
   ```bash
   php artisan serve
   ```

> **Akun Demo Testing:**
> - **Admin**: `admin@gmail.com` | `admin123`
> - **User**: `user@example.com` | `password`

## Lisensi
[MIT License](LICENSE)