SOFTWARE ARCHITECTURE DOCUMENT (SAD)

Field Attendance Management System (FAMS)

Version: 1.1
Status: Final
Date: July 2026

---

1. Introduction

Purpose

Dokumen ini mendefinisikan arsitektur teknis sistem Field Attendance Management System (FAMS) yang digunakan untuk mengelola absensi, dokumentasi lapangan, monitoring aktivitas, dan pelaporan.

Dokumen ini menjadi acuan bagi:

- Software Architect
- Backend Developer
- Frontend Developer
- DevOps Engineer
- QA Engineer
- AI Coding Agent

---

Scope

Mencakup:

- Arsitektur aplikasi
- Domain model
- Data flow
- Service architecture
- Security architecture
- Infrastructure architecture
- Deployment architecture

---

2. Architectural Goals

Maintainability

Sistem harus mudah dikembangkan dan dipelihara.

---

Scalability

Mampu melayani:

- 500+ user aktif
- Multi cabang
- Multi lokasi kerja

---

Reliability

Target uptime:

99%

---

Security

Melindungi:

- Data karyawan
- Data absensi
- Dokumentasi lapangan

---

Performance

Target:

- API Response < 500 ms
- Page Load < 3 detik

---

3. Architectural Style

Chosen Architecture

Modular Monolith

---

Reason

Laravel sangat cocok menggunakan Modular Monolith karena:

- Development lebih cepat
- Tidak kompleks seperti Microservice
- Mudah dipecah menjadi service terpisah di masa depan

---

Architecture Diagram

┌─────────────────────┐
│ Browser / Mobile PWA│
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Nginx Reverse Proxy │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Laravel Application │
└──────────┬──────────┘
           │
 ┌─────────┼─────────┐
 ▼         ▼         ▼

Attendance
Documentation
Reporting

           │
           ▼

     Service Layer

           │
           ▼

    Repository Layer

           │
           ▼

 PostgreSQL + PostGIS

           │
           ▼

       MinIO

---

4. System Context

External Actors

Employee

Melakukan absensi dan dokumentasi.

Supervisor

Monitoring dan approval.

HR Admin

Mengelola data dan laporan.

Super Admin

Mengelola sistem.

---

5. Domain Architecture

Employee Domain

Bertanggung jawab atas:

- Employee
- Division
- Position
- User Account

---

Attendance Domain

Bertanggung jawab atas:

- Check In
- Check Out
- Location Logging
- Attendance History

---

Documentation Domain

Bertanggung jawab atas:

- Dokumentasi Lapangan
- Upload Foto
- Aktivitas Lapangan

---

Approval Domain

Bertanggung jawab atas:

- Attendance Correction
- Approval Workflow

---

Reporting Domain

Bertanggung jawab atas:

- Daily Report
- Monthly Report
- Export

---

Notification Domain

Bertanggung jawab atas:

- Email Notification
- Future Push Notification

---

6. Layered Architecture

Presentation Layer

Komponen:

- Filament Admin Panel
- Employee Portal
- API Endpoint

Tanggung Jawab:

- Menampilkan data
- Menerima request

Tidak boleh berisi business logic.

---

Application Layer

Komponen:

- Services
- Actions
- Jobs

Tanggung Jawab:

- Menjalankan use case

---

Domain Layer

Komponen:

- Entities
- Value Objects
- Domain Services

Tanggung Jawab:

- Menyimpan aturan bisnis

---

Infrastructure Layer

Komponen:

- Database
- Cache
- Storage
- Queue

Tanggung Jawab:

- Implementasi teknis

---

7. Module Architecture

Authentication Module

Services:

- LoginService
- LogoutService
- PasswordResetService

---

Employee Module

Services:

- EmployeeService
- DivisionService
- PositionService

---

Attendance Module

Services:

- AttendanceService
- GeofenceService (legacy, not actively used for validation)
- AttendanceValidationService (legacy)

---

Documentation Module

Services:

- DocumentationService
- PhotoUploadService

---

Reporting Module

Services:

- ReportGeneratorService
- ExportService

---

Approval Module

Services:

- AttendanceCorrectionService
- ApprovalService

---

8. Service Layer Design

AttendanceService

Responsibilities:

- Check In
- Check Out
- Attendance History

Methods:

checkIn()

checkOut()

getHistory()

getTodayAttendance()

---

GeofenceService (Legacy)

Responsibilities:

- Radius Calculation (not actively used)

Methods:

calculateDistance()

---

DocumentationService

Responsibilities:

- Create Documentation
- Upload Photos

Methods:

storeDocumentation()

uploadPhotos()

---

9. Repository Pattern

Purpose

Memisahkan Business Logic dan Data Access.

---

Example:

EmployeeRepositoryInterface

EmployeeRepository

---

Flow:

Controller

↓

Service

↓

Repository

↓

Model

↓

Database

---

10. Queue Architecture

Queue Driver

Redis

---

Background Jobs

Generate Report

Job:

GenerateReportJob

---

Image Processing

Job:

ProcessDocumentationImageJob

---

Email Notification

Job:

SendNotificationJob

---

11. Storage Architecture

Storage Strategy

Development

Local Storage

---

Production

MinIO

---

Folder Structure:

employees/

attendance/

documentation/

exports/

---

File Naming

attendance/
2026/
06/
employee-id/
uuid.jpg

---

12. Database Architecture

Database Engine

PostgreSQL 17

---

Geospatial Engine

PostGIS

---

UUID Strategy

Semua tabel menggunakan UUID.

---

Soft Delete

Wajib untuk:

- Employees
- Documentation
- Attendance Corrections

---

13. Geolocation Architecture

GPS Source

Browser Geolocation API

---

Data Collected

latitude

longitude

accuracy

---

Note

Geofence validation tidak digunakan. Petugas lapangan bebas check-in dari mana saja.
Lokasi GPS tetap dicatat di attendance_logs untuk referensi.

---

14. Security Architecture

Authentication

Laravel Sanctum

---

Authorization

Spatie Permission

Roles:

- Super Admin
- HR Admin
- Supervisor
- Employee

---

Security Controls

HTTPS

Mandatory

CSRF Protection

Enabled

XSS Protection

Enabled

SQL Injection Protection

Laravel ORM

Rate Limiting

Enabled

---

15. Audit Architecture

Semua perubahan data dicatat.

---

Audit Log:

user_id

action

table_name

record_id

old_values

new_values

timestamp

---

16. Monitoring Architecture

Application Monitoring

Laravel Telescope

---

Error Monitoring

Sentry

---

Queue Monitoring

Laravel Horizon

---

17. Backup Architecture

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

18. Deployment Architecture

Internet

↓

Cloudflare

↓

Nginx

↓

Laravel Application

↓

Redis

↓

PostgreSQL

↓

MinIO

---

19. Scalability Strategy

Phase 1

Single Server

---

Phase 2

Dedicated Database Server

---

Phase 3

Load Balancer

Multiple App Servers

---

Phase 4

Kubernetes Deployment

---

20. Recommended Folder Structure

app/

├── Domains/
│
├── Services/
│
├── Repositories/
│
├── DTOs/
│
├── Actions/
│
├── Policies/
│
├── Events/
│
├── Listeners/
│
├── Jobs/
│
├── Notifications/
│
└── Support/

---

21. Coding Principles

Mandatory:

- SOLID
- DRY
- KISS
- Repository Pattern
- Service Layer Pattern
- DTO Pattern

Forbidden:

- Business Logic di Controller
- Query Database di Blade
- Query Database di Filament Resource

---

22. Future Architecture

Phase 2

- PWA
- Offline Attendance

Phase 3

- Realtime Tracking

Phase 4

- Face Verification

Phase 5

- Payroll Integration

---

23. Architecture Approval

Document Owner:
Software Architect

Reviewed By:
Lead Developer

Approved By:
Project Manager