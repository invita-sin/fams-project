# Field Attendance Management System (FAMS)

**FAMS** adalah aplikasi absensi berbasis GPS untuk staff kantor dan petugas lapangan dengan dokumentasi foto, monitoring aktivitas, dan pelaporan otomatis.

## Tech Stack

- **Backend:** PHP 8.4, Laravel 13
- **Admin Panel:** Filament 5, Livewire 3
- **Frontend:** TailwindCSS 4, AlpineJS
- **Database:** MariaDB/MySQL (dev), PostgreSQL 17 + PostGIS (production)
- **Queue:** Database (dev), Redis (production)
- **Storage:** Local (dev), MinIO/AWS S3 (production)

## Fitur Utama

- **Absensi** — Check In/Out dengan kamera + timestamp, GPS otomatis
- **Dokumentasi Lapangan** — Upload foto kegiatan dengan GPS
- **Koreksi Absensi** — Approval workflow (Supervisor)
- **Dashboard** — Monitoring per role (Admin, Supervisor, Employee)
- **Laporan** — Daily/Monthly, Export Excel/CSV/PDF
- **Audit Trail** — Semua perubahan tercatat

## Role & Credentials

| Role | Email | Password |
|---|---|---|
| Super Admin | admin@fams.test | password |
| HR Admin | hr_admin@fams.test | password |
| Supervisor | supervisor1-2@fams.test | password |
| Employee | employee1-5@fams.test | password |

**Default URL:** `/admin`

## Installation

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

## Testing

```bash
php artisan test
```
