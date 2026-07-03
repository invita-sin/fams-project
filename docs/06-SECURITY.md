SECURITY ARCHITECTURE & SECURITY POLICY

Field Attendance Management System (FAMS)

Version: 1.1
Status: Final
Date: July 2026

---

1. Purpose

Dokumen ini mendefinisikan standar keamanan yang wajib diterapkan pada seluruh komponen Field Attendance Management System (FAMS).

Tujuan utama:

- Melindungi data karyawan
- Melindungi data absensi
- Melindungi dokumentasi lapangan
- Mencegah manipulasi absensi
- Memenuhi standar keamanan aplikasi modern

---

2. Security Objectives

Confidentiality

Hanya pengguna yang berwenang dapat mengakses data.

---

Integrity

Data tidak boleh dimanipulasi tanpa otorisasi.

---

Availability

Sistem tetap tersedia dan dapat digunakan.

---

Accountability

Seluruh aktivitas pengguna harus dapat diaudit.

---

3. Security Principles

Principle of Least Privilege

Pengguna hanya memiliki akses minimum yang diperlukan.

---

Defense in Depth

Keamanan diterapkan di setiap lapisan sistem.

---

Secure by Default

Konfigurasi default harus aman.

---

Zero Trust

Setiap request harus divalidasi.

---

4. Authentication Security

Authentication Method

Laravel Sanctum

---

Password Policy

Minimum:

8 karakter

Direkomendasikan:

12+ karakter

Harus mengandung:

- Huruf besar
- Huruf kecil
- Angka

---

Password Hashing

Wajib menggunakan:

Argon2ID

Fallback:

Bcrypt

---

Session Policy

Idle Timeout:

60 menit

Remember Login:

30 hari

---

5. Authorization Security

Framework

Spatie Permission

---

Roles

Super Admin

Full Access

---

HR Admin

Employee Management

Reporting

---

Supervisor

Approval

Monitoring

---

Employee

Attendance

Documentation

---

6. API Security

Authentication Header

Authorization: Bearer TOKEN

---

Rate Limiting

Authentication:

5 request / menit

---

Attendance:

60 request / menit

---

API Global:

120 request / menit

---

API Versioning

/api/v1/

---

7. Database Security

Database Access

Hanya server aplikasi yang boleh mengakses database.

---

Remote Access

Production:

Disabled

---

Database User Policy

Gunakan user terpisah:

app_user
backup_user
readonly_user

---

SQL Injection Protection

Mandatory:

- Eloquent ORM
- Query Builder
- Parameter Binding

Forbidden:

DB::select("SELECT * FROM users WHERE id=$id");

---

8. File Upload Security

Allowed Formats

Attendance Photos

jpg
jpeg
png
webp

---

Documentation Photos

jpg
jpeg
png
webp

---

Forbidden Formats

php
js
exe
sh
bat
apk
jar

---

File Size Limits

Attendance Photo

5 MB

---

Documentation Photo

10 MB

---

Virus Scanning

Future Phase:

ClamAV

---

9. Storage Security

Storage Engine

MinIO

atau

AWS S3

---

Private Storage

Default:

private

---

Public URL

Menggunakan:

Signed URL

---

URL Expiration

5 menit

---

10. GPS Security

Location Validation

Wajib menyimpan:

Latitude
Longitude
Accuracy
Timestamp

---

Accuracy Validation

Tidak diterapkan (dev). GPS accuracy tetap dicatat untuk referensi.

---

Geofence Validation

Tidak digunakan. Petugas lapangan bebas check-in dari mana saja.
Lokasi GPS tetap dicatat untuk audit.

---

11. Anti Fake GPS Strategy

Detection Methods

Simpan:

GPS Accuracy
IP Address
Device Information
User Agent

---

Suspicious Indicators

Accuracy:

> 100 meter

---

Location Jump:

> 10 km dalam 1 menit

---

Audit Flag

Jika terdeteksi:

suspicious = true

---

12. Attendance Photo Security

Mandatory

Selfie wajib diambil saat absensi dengan timestamp terbakar di foto.

---

Metadata Storage

Simpan:

File Hash
Upload Time
File Size
Mime Type
Timestamp (burned into image via canvas)

---

Duplicate Detection

Future Phase:

Perbandingan hash file.

---

13. CSRF Protection

Enabled by Laravel.

Mandatory untuk:

- Web Forms
- Filament Forms

---

14. XSS Protection

Input Validation

Semua input wajib divalidasi.

---

Output Escaping

Gunakan:

{{ $value }}

Forbidden:

{!! $value !!}

Kecuali sudah disanitasi.

---

15. CORS Policy

Production:

Allowed Origins:

https://attendance.company.com

Forbidden:

*

---

16. Audit Logging

Mandatory Audit Events

Login

Logout

Check In

Check Out

Create Documentation

Update Documentation

Correction Approval

Role Changes

User Deletion

---

Audit Data

User ID
Event Type
IP Address
Timestamp
Old Value
New Value

---

17. Monitoring Security

Telescope

Environment:

local
staging

Forbidden:

production

---

Sentry

Enabled:

production

---

18. Queue Security

Queue Driver

Redis

---

Queue Isolation

Separate Queues:

default

reports

images

notifications

---

19. Secret Management

Forbidden

Hardcoded Secret

Example:

DB_PASSWORD=password123

---

Required

Environment Variables

APP_KEY=
DB_PASSWORD=
REDIS_PASSWORD=
MINIO_SECRET_KEY=

---

20. Backup Security

Database Backup

Schedule:

Daily

Retention:

30 Days

---

File Backup

Schedule:

Daily

Retention:

30 Days

---

Backup Encryption

Mandatory:

AES-256

---

21. Server Security

Operating System

Ubuntu Server 24.04 LTS

---

Firewall

UFW

Allowed:

80
443
22

---

SSH Security

Disable:

root login
password login

Enable:

ssh key authentication

---

22. HTTPS Policy

Mandatory.

SSL Provider:

- Let's Encrypt
- Cloudflare Origin Certificate

---

HTTP Redirect

Force:

HTTP -> HTTPS

---

23. Security Headers

Mandatory Headers:

X-Frame-Options: DENY

X-Content-Type-Options: nosniff

Referrer-Policy: strict-origin

Content-Security-Policy

---

24. Security Testing

Required:

SAST

Laravel Pint

Larastan

---

DAST

OWASP ZAP

---

Dependency Scanning

Composer Audit

GitHub Dependabot

---

25. Incident Response

Severity Levels

Critical

Database Breach

Target Response:

< 1 Jam

---

High

Unauthorized Access

Target Response:

< 4 Jam

---

Medium

Bug Exploitation

Target Response:

< 24 Jam

---

26. Compliance Checklist

Before Production Release

✓ HTTPS Enabled

✓ Rate Limiting Enabled

✓ Audit Log Enabled

✓ Backup Enabled

✓ Firewall Enabled

✓ Sentry Enabled

✓ Secrets Protected

✓ File Upload Validation Enabled

✓ Geofence Validation Enabled

✓ GPS Accuracy Validation Enabled

✓ Security Testing Passed

---

27. Future Security Roadmap

Phase 2

- Two Factor Authentication (2FA)
- Device Registration

---

Phase 3

- Face Verification
- Anti Spoofing Detection

---

Phase 4

- SIEM Integration
- Centralized Security Monitoring

---

Approval

Document Owner:
Security Architect

Reviewed By:
Lead Backend Engineer

Approved By:
Software Architect