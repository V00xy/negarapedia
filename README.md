<p align="center"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></p>

## Tentang Negarapedia

Negarapedia adalah aplikasi web berbasis **Laravel 13** yang menyediakan informasi lengkap tentang negara-negara di dunia. Aplikasi ini memanfaatkan **REST Countries API** untuk menampilkan data negara secara real-time dan **Groq AI** untuk fitur tanya jawab interaktif seputar negara.

### Fitur Utama

- 🔍 **Pencarian Negara** — Cari informasi detail tentang berbagai negara di dunia
- ⭐ **Favorit Negara** — Simpan dan kelola daftar negara favorit
- 🏁 **Kuis Tebak Bendera** — Uji pengetahuanmu tentang bendera negara-negara dunia
- 🏆 **Leaderboard** — Lihat peringkat pemain kuis
- 🤖 **AI Chat Negara** — Tanya jawab interaktif tentang negara dengan Groq AI
- 🔐 **Autentikasi** — Registrasi dan login pengguna

### Tech Stack

- **Backend:** Laravel 13, PHP 8.3+
- **Frontend:** Tailwind CSS v4, Vite
- **Database:** SQLite
- **API External:** REST Countries API, Groq AI API

## Persyaratan Sistem

- PHP ^8.3
- Composer
- Node.js & npm
- SQLite

## Instalasi

```bash
# Clone repositori
git clone https://github.com/username/negarapedia.git
cd negarapedia

# Setup aplikasi
composer run setup

# Jalankan server development
composer run dev
```

Atau jalankan secara manual:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

## Menjalankan Aplikasi

```bash
composer run dev
```

Perintah di atas akan menjalankan:
- Server Laravel (`php artisan serve`)
- Queue worker (`php artisan queue:listen`)
- Vite dev server (`npm run dev`)

## Menjalankan Test

```bash
composer run test
```

## Lisensi

[MIT](https://opensource.org/licenses/MIT)
