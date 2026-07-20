# Analisis Studi Kasus: Mitigasi Risiko Rantai Pasok Global dengan RiskIntel

Dokumen ini disusun untuk menjelaskan bagaimana platform **RiskIntel (Supply Chain Risk Intelligence)** menganalisis, memetakan, dan memitigasi lima masalah utama yang dihadapi oleh perusahaan importir dalam melakukan pengiriman barang dari berbagai negara.

---

## Ringkasan Eksekutif
Dalam perdagangan global, kelancaran rantai pasok sangat dipengaruhi oleh faktor lingkungan, ekonomi, dan geopolitik. Platform **RiskIntel** menyediakan solusi berbasis data dengan memadukan metrik real-time dan **Weighted Composite Risk Scoring Engine** (Model Risiko Terbobot) untuk memberikan keputusan strategis bagi importir.

---

## Pemetaan 5 Masalah Utama & Solusi Platform

### 1. Cuaca Buruk yang Mengganggu Pengiriman (Weather Risk)
*   **Bagaimana Platform Menganalisis**:
    *   RiskIntel memantau data cuaca real-time (suhu, curah hujan, kecepatan angin, dan risiko badai) melalui model `Weather`.
    *   **Formula Kalkulasi** (`RiskScoringService::calculateWeatherScore`):
        $$\text{Skor Risiko Cuaca} = (\text{wind\_speed} \times 1.5) + (\text{rain} \times 0.5) + \text{storm\_risk}$$
        *(Hasil akhir dibatasi dalam rentang 0 - 100)*.
*   **Strategi Mitigasi bagi Importir**:
    *   Importir dapat melihat visualisasi data cuaca langsung pada detail negara.
    *   Jika skor risiko cuaca meningkat (misalnya saat terjadi fenomena La Nina atau musim badai), importir dapat menjadwalkan ulang pengiriman atau beralih ke rute pelayaran alternatif yang lebih aman.

---

### 2. Perubahan Nilai Tukar Mata Uang (Currency Risk)
*   **Bagaimana Platform Menganalisis**:
    *   Platform melacak nilai tukar harian terhadap USD melalui model `Currency`.
    *   **Formula Volatilitas** (`RiskScoringService::calculateCurrencyScore`):
        Mengukur koefisien variasi (perbedaan antara nilai tertinggi dan terendah relatif terhadap rata-rata) nilai tukar mata uang lokal selama 30 hari terakhir menggunakan data historis dari *Frankfurter API*:
        $$\text{Volatilitas} = \frac{\text{Max Rate} - \text{Min Rate}}{\text{Min Rate}}$$
        $$\text{Skor Risiko Valas} = \text{Volatilitas} \times 500.0$$
*   **Strategi Mitigasi bagi Importir**:
    *   Nilai tukar yang fluktuatif (skor risiko tinggi) memperingatkan importir akan potensi pembengkakan biaya impor.
    *   Importir dapat melakukan tindakan *hedging* (lindung nilai) valuta asing dengan bank atau memilih negara eksportir dengan mata uang yang lebih stabil.

---

### 3. Konflik Geopolitik yang Meningkatkan Risiko (Geopolitical & Sentiment Risk)
*   **Bagaimana Platform Menganalisis**:
    *   Platform secara berkala mengambil berita rantai pasok dari *GNews API* dan menyimpannya di model `News`.
    *   **Analisis Sentimen Otomatis** (`SentimentService` & `RiskScoringService::calculateSentimentScore`):
        Sistem memindai konten berita menggunakan metode leksikon untuk menghitung kata positif dan negatif (seperti *war*, *conflict*, *sanction*, *tariff*, *tension*).
        $$\text{Skor Sentimen Geopolitik} = \left( \frac{\text{Jumlah Berita Negatif}}{\text{Total Berita}} \right) \times 100.0$$
*   **Strategi Mitigasi bagi Importir**:
    *   Jika sentimen geopolitik suatu negara memburuk (skor tinggi), importir dapat mendeteksi potensi sanksi dagang, embargo, atau tarif baru lebih awal.
    *   Memungkinkan importir untuk mendiversifikasi pemasok sebelum konflik memuncak.

---

### 4. Kemacetan Pelabuhan yang Menyebabkan Keterlambatan (Port Congestion)
*   **Bagaimana Platform Menganalisis**:
    *   RiskIntel memetakan titik koordinat pelabuhan utama dunia menggunakan *Leaflet.js* (model `Port`) agar importir dapat melihat alternatif pelabuhan terdekat di wilayah tersebut.
    *   Kemacetan pelabuhan dideteksi melalui analisis sentimen berita logistik dengan kata kunci spesifik seperti *congestion*, *delay*, *bottleneck*, *strike*, dan *blockade* yang secara otomatis menaikkan Skor Risiko Sentimen negara terkait.
*   **Strategi Mitigasi bagi Importir**:
    *   Melalui peta interaktif, importir di Indonesia dapat melihat alternatif pelabuhan pengiriman. Contohnya di Jerman, jika *Port of Hamburg* mengalami kemacetan, sistem dapat mengarahkan opsi pengiriman ke *Port of Bremen* atau *Port of Wilhelmshaven*.
    *   Memantau rilis artikel mitigasi risiko di platform untuk mengetahui estimasi waktu keterlambatan.

---

### 5. Inflasi Negara Eksportir Mempengaruhi Biaya Produksi (Inflation Risk)
*   **Bagaimana Platform Menganalisis**:
    *   Platform melakukan sinkronisasi data inflasi tahunan dari *World Bank API* (model `Inflation`).
    *   **Formula Deviasi** (`RiskScoringService::calculateInflationScore`):
        Mengukur seberapa jauh inflasi negara tersebut melenceng dari standar ideal inflasi dunia sehat (2.0%):
        $$\text{Skor Risiko Inflasi} = | \text{Laju Inflasi} - 2.0 | \times 5.0$$
*   **Strategi Mitigasi bagi Importir**:
    *   Inflasi yang terlalu tinggi meningkatkan harga bahan baku dan upah tenaga kerja di negara asal, yang pada akhirnya menaikkan harga jual barang impor.
    *   Data tren inflasi 5 tahun terakhir membantu importir dalam negosiasi kontrak jangka panjang atau memindahkan sumber pasokan ke negara dengan tingkat inflasi yang lebih rendah dan stabil.

---

## Visualisasi Penilaian Risiko Komposit (Composite Risk Index)
RiskIntel menggabungkan kelima parameter di atas menjadi satu skor tunggal yaitu **Total Score** dengan bobot seimbang (masing-masing 25%):
$$\text{Total Risk Score} = (\text{Weather} \times 0.25) + (\text{Inflation} \times 0.25) + (\text{Currency} \times 0.25) + (\text{Sentiment} \times 0.25)$$

### Kategori Risiko:
*   🟢 **Low Risk (Skor < 25)**: Negara sangat aman untuk transaksi impor jangka panjang.
*   🟡 **Medium Risk (Skor 25 - 49)**: Diperlukan pemantauan berkala terhadap indikator sensitif.
*   🔴 **High Risk (Skor >= 50)**: Memerlukan tindakan mitigasi segera atau pengalihan pemasok.

Dengan arsitektur ini, perusahaan importir memiliki alat navigasi berbasis data ilmiah untuk meminimalkan kerugian operasional dan finansial dalam rantai pasok mereka.
