PRODUCT REQUIREMENTS DOCUMENT (PRD)

Field Attendance Management System (FAMS)

Version: 2.1
Status: Final
Date: July 2026

---

1. Executive Summary

Field Attendance Management System (FAMS) adalah aplikasi web berbasis GPS yang digunakan untuk mencatat kehadiran staff kantor dan petugas lapangan secara akurat melalui dokumentasi foto dengan timestamp, pencatatan lokasi GPS, serta monitoring aktivitas lapangan.

Sistem dirancang untuk mengurangi manipulasi absensi, meningkatkan akurasi pelaporan, serta memudahkan pengawasan operasional perusahaan.

---

2. Background

Banyak perusahaan yang memiliki petugas lapangan mengalami kesulitan dalam memastikan bahwa karyawan benar-benar hadir di lokasi kerja yang ditentukan.

Permasalahan yang sering terjadi:

- Titip absen.
- Manipulasi lokasi.
- Dokumentasi pekerjaan tidak terdokumentasi.
- Sulit melakukan monitoring petugas lapangan.
- Rekap absensi dilakukan secara manual.

FAMS hadir sebagai solusi dengan mengintegrasikan GPS, dokumentasi foto dengan timestamp, pencatatan lokasi, dan pelaporan otomatis.

---

3. Product Vision

Menjadi platform absensi dan monitoring lapangan yang akurat, mudah digunakan, dan dapat diandalkan untuk perusahaan yang memiliki operasional lapangan.

---

4. Product Goals

Goal Utama

- Digitalisasi proses absensi.
- Validasi lokasi absensi menggunakan GPS.
- Menyediakan dokumentasi aktivitas lapangan.
- Menyediakan dashboard monitoring.
- Menghasilkan laporan otomatis.

KPI

- 100% absensi tercatat digital.
- Akurasi lokasi absensi > 95%.
- Pengurangan manipulasi absensi.
- Waktu pembuatan laporan berkurang 90%.

---

5. Scope Product

In Scope

Attendance Management

- Check In
- Check Out
- Selfie Verification
- GPS Location Capture
- Timestamp Photo

Employee Management

- Employee CRUD
- Division Management
- Position Management

Field Documentation

- Upload foto kegiatan
- Upload multi foto
- Catatan aktivitas
- Lokasi GPS

Reporting

- Daily Report
- Weekly Report
- Monthly Report
- Export Excel
- Export PDF

Monitoring

- Dashboard Monitoring
- Activity Monitoring
- Attendance Monitoring

Approval

- Attendance Correction
- Supervisor Approval

---

Out of Scope (Phase 1)

- Payroll
- Face Recognition AI
- Mobile Native Application
- Fingerprint Integration

---

6. User Roles

Super Admin

Hak akses penuh terhadap sistem.

Permission

- Manage Users
- Manage Roles
- Manage Settings
- Manage Reports
- Manage Work Locations

---

HR Admin

Permission

- Manage Employees
- View Reports
- Export Reports

---

Supervisor

Permission

- View Team Attendance
- Approve Corrections
- Monitor Team Activities

---

Employee

Permission

- Check In
- Check Out
- Upload Documentation
- View Own History

---

7. Functional Requirements

Authentication Module

Features

- Login
- Logout
- Forgot Password
- Change Password

Validation

- Email wajib unik.
- Password minimal 8 karakter.

---

Employee Management Module

Features

- Create Employee
- Update Employee
- Delete Employee
- Employee Status

Data

- Employee Code
- Name
- Email
- Phone
- Division
- Position

---

Attendance Module

Check In

Input:

- GPS Location
- Selfie Photo with Timestamp

Output:

- Check In Time
- Coordinates
- Photo with Timestamp

Validation:

- Foto wajib.
- GPS dicatat untuk referensi lokasi.

---

Check Out

Input:

- GPS Location
- Selfie Photo with Timestamp

Output:

- Check Out Time
- Coordinates
- Photo with Timestamp

Validation:

- Foto wajib.
- GPS dicatat untuk referensi lokasi.

---

Documentation Module

Features

- Upload Documentation
- Upload Multi Photo
- Activity Notes

Data:

- Title
- Description
- Latitude
- Longitude
- Activity Date

---

Attendance Correction Module

Features

- Submit Correction
- Approval Workflow

Status:

- Pending
- Approved
- Rejected

---

Reporting Module

Reports

- Attendance Daily
- Attendance Monthly
- Employee Activity
- Field Documentation

Export:

- Excel
- PDF
- CSV

---

8. Non Functional Requirements

Performance

- Response Time < 3 Seconds
- Support 500 Concurrent Users

---

Security

- HTTPS
- CSRF Protection
- Rate Limiting
- Audit Logging

---

Availability

- Uptime 99%

---

Scalability

- Ready for Multi Branch Deployment
- Ready for Future Mobile App

---

9. Technology Stack

Backend

- PHP 8.4+
- Laravel 13

Admin Panel

- Filament 5

Frontend

- Livewire 3
- TailwindCSS 4
- AlpineJS

Database

Recommended:

PostgreSQL 17 + PostGIS

Alternative:

MySQL 8

Storage

- MinIO
- AWS S3 Compatible

Queue

- Redis

Monitoring

- Laravel Horizon
- Telescope
- Sentry

CI/CD

- GitHub Actions

---

10. MVP Deliverables

Authentication

✓ Login

✓ Logout

Employee Management

✓ CRUD Employee

✓ Division

✓ Position

Attendance

✓ Check In

✓ Check Out

✓ GPS Location Capture

✓ Timestamp Photo

Documentation

✓ Upload Documentation

✓ Multi Photo Upload

Reporting

✓ Daily Report

✓ Monthly Report

✓ Export Excel

Monitoring

✓ Dashboard

---

11. Future Roadmap

Phase 2

- Shift Management
- Leave Management
- Push Notification
- Email Notification

Phase 3

- Progressive Web App (PWA)
- Offline Attendance
- Realtime Tracking

Phase 4

- Face Verification
- AI Attendance Validation
- Payroll Integration

---

12. Success Criteria

Sistem dianggap berhasil apabila:

- Seluruh absensi dilakukan melalui sistem.
- Tidak terdapat manipulasi lokasi absensi.
- Dokumentasi lapangan terdigitalisasi.
- Supervisor dapat memonitor aktivitas lapangan secara real-time.
- HR dapat menghasilkan laporan otomatis tanpa proses manual.

---

13. Approval

Document Owner:
Product Owner

Reviewed By:
Project Manager

Approved By:
Management