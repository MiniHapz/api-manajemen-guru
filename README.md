# API Manajemen Guru & Sekolah

Backend REST API menggunakan **Laravel 12** untuk mengelola data guru, sekolah, mapel, pengguna (admin & operator), serta dashboard statistik.

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

---

## 🚀 Fitur Utama

- ✅ Autentikasi (Laravel Sanctum)  
- ✅ CRUD Guru & Sekolah  
- ✅ Relasi Mapel – Guru – Sekolah  
- ✅ Dashboard Admin (statistik guru & sekolah)  
- ✅ Dashboard Operator (data sekolah masing-masing)  
- ✅ Hitung guru mendekati masa pensiun  

---
## 🛠️ Teknologi

- Laravel 12  
- MySQL  
- Sanctum  
- Eloquent ORM  
- Carbon

---

## 🔧 Instalasi

```bash
git clone https://github.com/MiniHapz/api-manajemen-guru.git
cd api-manajemen-guru

composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

Langsung siap jalankan
```bash
php artisan serve