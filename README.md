# TelkomCare Dashboard 📈



> ### Sebuah aplikasi *dashboard* Laravel untuk memantau gangguan internet, menyediakan fitur filter data dan penambahan data.

Repositori ini fungsional dan siap digunakan. Permintaan *pull request* dan laporan *issue* sangat kami hargai!

---

## 🚀 Memulai Proyek

Ikuti langkah-langkah di bawah ini untuk menginstal dan menjalankan proyek TelkomCare Dashboard di lingkungan lokal Anda.

### Persyaratan Sistem

Pastikan sistem Anda memenuhi persyaratan berikut:

* **PHP:** ^7.4 | ^8.0 (disarankan PHP 8.x)
* **Composer:** Versi terbaru
* **Node.js & npm/Yarn:** Versi terbaru (untuk kompilasi *asset frontend*)
* **Web Server:** Apache atau Nginx (dengan konfigurasi yang sesuai untuk Laravel)
* **Database:** MySQL (atau MariaDB)
* **phpMyAdmin:** Untuk pengelolaan *database* (opsional, tapi disarankan)

### Langkah-langkah Instalasi

1.  **Kloning Repositori:**
    Buka terminal atau *command prompt* Anda, lalu *kloning* repositori proyek:

    ```bash
    git clone [https://github.com/faizalma7/telkomcare_dashboard.git](https://github.com/faizalma7/telkomcare_dashboard.git)
    cd dashboard-tcare
    ```

2.  **Instal Dependensi Composer:**
    Masuk ke direktori proyek dan instal semua dependensi PHP menggunakan Composer:

    ```bash
    composer install
    ```

3.  **Buat File `.env`:**
    Duplikasi file `.env.example` menjadi `.env`. Ini akan menjadi tempat konfigurasi lingkungan Anda.

    ```bash
    cp .env.example .env
    ```

4.  **Buat *Application Key*:**
    *Generate* kunci aplikasi Laravel. Ini penting untuk keamanan aplikasi Anda.

    ```bash
    php artisan key:generate
    ```

5.  **Konfigurasi Database:**
    Buka file `.env` dan sesuaikan pengaturan *database* Anda. Pastikan nama *database* sesuai dengan yang akan Anda impor nanti.

    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=telkomcare_dashboard # Pastikan nama ini sesuai!
    DB_USERNAME=root
    DB_PASSWORD=
    ```
    Ganti `DB_USERNAME` dan `DB_PASSWORD` sesuai dengan kredensial *database* lokal Anda.

6.  **Instal Dependensi Node.js:**
    Instal dependensi *frontend* menggunakan npm atau Yarn:

    ```bash
    npm install
    # ATAU
    yarn install
    ```

7.  **Kompilasi Asset Frontend:**
    Kompilasi *asset* CSS dan JavaScript. Untuk pengembangan, Anda bisa menggunakan `npm run dev`. Untuk produksi, gunakan `npm run prod`.

    ```bash
    npm run dev
    # ATAU
    yarn dev
    ```
    Jika Anda ingin mengawasi perubahan file dan mengkompilasinya secara otomatis:
    ```bash
    npm run watch
    # ATAU
    yarn watch
    ```

8.  **Jalankan Server Lokal (opsional):**
    Anda bisa menggunakan *server* pengembangan bawaan Laravel:

    ```bash
    php artisan serve
    ```
    Aplikasi Anda akan dapat diakses di `http://127.0.0.1:8000`.

---

## 🗄️ Menambahkan Database ke phpMyAdmin

Proyek ini sudah dilengkapi dengan *dump database* bernama **`telkomcare_dashboard.sql`**. Ikuti langkah-langkah di bawah ini untuk mengimpornya ke phpMyAdmin Anda.

1.  **Akses phpMyAdmin:**
    Buka *browser* Anda dan navigasikan ke alamat phpMyAdmin Anda (biasanya `http://localhost/phpmyadmin/`).

2.  **Buat Database Baru:**
    * Di sisi kiri phpMyAdmin, klik tombol **"New"** atau **"Baru"**.
    * Masukkan nama *database* sesuai yang Anda konfigurasikan di file `.env`, yaitu **`telkomcare_dashboard`**.
    * Pilih *collation* yang sesuai (misalnya `utf8mb4_unicode_ci` disarankan).
    * Klik tombol **"Create"** atau **"Buat"**.

3.  **Impor File SQL:**
    * Setelah *database* `telkomcare_dashboard` berhasil dibuat, klik nama *database* tersebut di panel kiri untuk memilihnya.
    * Di bagian atas halaman, klik tab **"Import"** atau **"Impor"**.
    * Klik tombol **"Choose File"** atau **"Pilih File"** dan navigasikan ke lokasi file `telkomcare_dashboard.sql` yang ada di direktori proyek Anda (pastikan Anda menemukannya di *root* direktori proyek atau di dalam folder `database/dump` jika ada).
    * Biarkan opsi lainnya pada nilai *default* jika Anda tidak yakin.
    * Gulir ke bawah dan klik tombol **"Go"** atau **"Kirim"**.

4.  **Verifikasi:**
    Jika impor berhasil, Anda akan melihat pesan sukses dan tabel-tabel *database* akan muncul di bawah *database* `telkomcare_dashboard` di sisi kiri phpMyAdmin.

---

## ✅ Siap Digunakan!

Setelah semua langkah di atas selesai, proyek TelkomCare Dashboard Anda seharusnya sudah berjalan dengan baik. Anda dapat mengaksesnya melalui alamat *server* lokal yang Anda gunakan (misalnya `http://127.0.0.1:8000`).

---

## 🛠️ Ikhtisar Kode

### Dependensi Utama

* **Tailwind CSS:** Untuk *styling* *utility-first*.
* **Font Awesome:** Untuk ikon.
* **Custom JavaScript:** Untuk logika *frontend* interaktif.

### Struktur Folder Penting

* `public/css/dashboard.css`: *Styling* kustom proyek.
* `public/js/dashboard.js`: Logika JavaScript *frontend* proyek.
* `resources/views/dashboard.blade.php`: *Template* utama *dashboard*.
* `database/dump/telkomcare_dashboard.sql`: *Dump database* awal.

---

## 🧪 Pengujian API

Proyek ini diharapkan berinteraksi dengan *endpoint* API untuk mengambil dan mengirim data.

### Request Headers (Contoh)

| **Required** | **Key** | **Value** |
| :----------- | :--------------- | :----------------- |
| Yes          | `Content-Type`   | `application/json` |
| Yes          | `X-Requested-With` | `XMLHttpRequest`   |
| Optional     | `X-CSRF-TOKEN`   | `{YOUR_CSRF_TOKEN}`|

---

## 🔒 Otentikasi

Aplikasi ini menggunakan Laravel's CSRF token untuk perlindungan *form* dan validasi. Pastikan token CSRF dikirim dengan setiap permintaan POST/PUT/DELETE.

---

## 🌐 Cross-Origin Resource Sharing (CORS)

Aplikasi ini mungkin memerlukan konfigurasi CORS yang tepat jika *frontend* di-hosting di domain yang berbeda. Konfigurasi CORS dapat diatur dalam *middleware* Laravel.
