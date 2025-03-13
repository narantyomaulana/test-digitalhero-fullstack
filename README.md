# Test Full Stack Developer DigitalHero (PS Rental System - Laravel 11)

Aplikasi ini merupakan sistem booking sederhana untuk layanan rental PlayStation yang terintegrasi dengan Midtrans sebagai payment gateway.

## Fitur Utama

### A. Calendar View
- Pengguna dapat memilih tanggal booking melalui tampilan kalender interaktif (menggunakan FullCalendar)
- Visualisasi tanggal yang dipilih
- Validasi tanggal (tidak bisa memilih tanggal yang sudah lewat)

### B. Pemilihan Jasa Rental
- Opsi pemilihan layanan:
  - Rental PS 4 (Rp 30.000 per sesi)
  - Rental PS 5 (Rp 40.000 per sesi)
- Detail layanan yang jelas dengan harga

### C. Perhitungan Biaya
- Tarif dasar:
  - PS 4: Rp 30.000 per sesi
  - PS 5: Rp 40.000 per sesi
- Weekend surcharge:
  - Tambahan Rp 50.000 jika pemesanan dilakukan pada hari Sabtu atau Minggu
- Total biaya dihitung secara otomatis berdasarkan pilihan pengguna dan tanggal pemesanan
- Tampilan perhitungan real-time saat pengguna memilih layanan dan tanggal

### D. Pembayaran via Midtrans
- Integrasi sistem pembayaran menggunakan Midtrans (mode sandbox)
- Popup pembayaran Midtrans yang user-friendly
- Handling notifikasi dan callback dari Midtrans
- Halaman konfirmasi booking setelah pembayaran berhasil

### E. Dashboard Admin Sederhana
- Melihat semua booking yang ada
- Melihat status pembayaran dari setiap booking
- Filter dan paginasi

## Teknologi yang Digunakan

- **Backend**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL
- **Frontend**:
  - Bootstrap 5
  - JavaScript/jQuery
  - FullCalendar untuk kalender interaktif
- **Payment Gateway**: Midtrans API (Sandbox Mode)