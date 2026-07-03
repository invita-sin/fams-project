TECHNICAL BUILD SPECIFICATION (TBS)

Field Attendance Management System (FAMS)

Version: 1.1
Status: Final
Date: July 2026

---

1. Purpose

Dokumen ini menjelaskan spesifikasi implementasi teknis seluruh modul FAMS.

Dokumen ini menjadi acuan bagi:

- Backend Developer
- Frontend Developer
- QA Engineer
- DevOps Engineer
- AI Coding Agent (Cursor, Claude Code, OpenCode AI)

---

2. Technical Architecture Standards

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

- PostgreSQL 17
- PostGIS

Queue

- Redis

Storage

- MinIO

---

3. Folder Structure

app/

├── Actions/
│
├── DTOs/
│
├── Domains/
│   ├── Attendance/
│   ├── Employee/
│   ├── Documentation/
│   ├── Approval/
│   ├── Report/
│   └── Notification/
│
├── Services/
│
├── Repositories/
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

4. Authentication Module

Objective

Mengelola autentikasi dan otorisasi sistem.

---

Filament Resources

UserResource

Features:

- Create User
- Edit User
- Delete User
- Assign Role

Fields:

- Employee
- Email
- Password
- Role

---

Services

AuthenticationService

Methods:

login()

logout()

resetPassword()

---

Policies

UserPolicy

Permissions:

view_user

create_user

update_user

delete_user

---

5. Employee Management Module

Objective

Mengelola data karyawan.

---

Filament Resource

EmployeeResource

Features:

- Create Employee
- Update Employee
- Archive Employee

---

Form Fields

Employee Information

Employee Code

Full Name

Phone

Email

Gender

Branch

Division

Department

Position

Hire Date

Status

---

Table Columns

Employee Code

Name

Division

Position

Status

---

Services

EmployeeService

Methods:

createEmployee()

updateEmployee()

deactivateEmployee()

getEmployee()

---

Repository

EmployeeRepository

Methods:

findById()

findByCode()

paginate()

create()

update()

---

6. Branch Module

Resource

BranchResource

Fields:

Code

Name

Address

Latitude

Longitude

Status

---

7. Division Module

Resource

DivisionResource

Fields:

Name

Branch

---

8. Department Module

Resource

DepartmentResource

Fields:

Name

Division

---

9. Position Module

Resource

PositionResource

Fields:

Name

Department

---

10. Work Location Module

Objective

Mengelola lokasi kerja.

---

Resource

WorkLocationResource

Fields:

Name

Address

Latitude

Longitude

Radius Meter

Active

---

Services

GeofenceService

Methods:

validateLocation()

calculateDistance()

findNearestLocation()

---

11. Attendance Module

Objective

Mencatat absensi.

---

Employee Portal Page

Check In Page (Employee Dashboard)

Flow:

- Floating button "Check In" di pojok kanan bawah
- Klik → modal kamera terbuka (AlpineJS + getUserMedia)
- Kamera menampilkan preview + timestamp real-time
- Klik "Ambil Foto" → foto di-capture dengan timestamp dibakar di pixel
- Base64 dikirim ke Livewire method → AttendanceService::checkIn()
- GPS dicatat di attendance_logs tanpa validasi radius
- Redirect halaman → stat terbaru

---

Validation

Photo wajib (dengan timestamp).

GPS dicatat untuk referensi lokasi.

---

Check In Workflow

User Click Check In (Floating Button)

↓

Camera Modal Opens (getUserMedia)

↓

Capture Photo with Timestamp Overlay

↓

GPS Capture (background)

↓

Create Attendance

↓

Store Attendance Log (with GPS)

↓

Redirect → Success

---

Check Out Workflow

User Click Check Out

↓

GPS Capture

↓

Photo Upload

↓

Update Attendance

↓

Store Log

↓

Success

---

Services

AttendanceService

Methods:

checkIn()

checkOut()

getHistory()

getTodayAttendance()

---

AttendanceValidationService

Methods:

validateGps()

validateGeofence()

validatePhoto()

---

Repository

AttendanceRepository

Methods:

create()

update()

findToday()

getHistory()

---

12. Attendance Correction Module

Objective

Mengelola koreksi absensi.

---

Resource

AttendanceCorrectionResource

Fields:

Attendance

Reason

Attachment

---

Status

Pending

Approved

Rejected

---

Services

AttendanceCorrectionService

Methods:

submit()

approve()

reject()

---

13. Documentation Module

Objective

Mencatat aktivitas lapangan.

---

Employee Page

Create Documentation

Fields:

Title

Category

Description

GPS

Photos

---

Upload Rules

Photo Format:

jpg

jpeg

png

webp

Maximum:

20 files

---

Services

DocumentationService

Methods:

store()

uploadPhotos()

delete()

---

Jobs

ProcessDocumentationImageJob

Responsibilities:

- Resize
- Compress
- Generate Thumbnail

---

14. Dashboard Module

Admin Dashboard

Widgets:

Employees Count

Attendance Today

Absent Today

Late Today

Documentation Today

---

Supervisor Dashboard

Widgets:

Team Attendance

Pending Approval

Documentation Count

---

Employee Dashboard

Widgets:

Today's Attendance

Monthly Attendance

Recent Documentation

---

15. Reporting Module

Objective

Membuat laporan.

---

Resource

ReportResource

Filters:

Date Range

Employee

Division

Branch

---

Report Types

Attendance Daily

Attendance Monthly

Employee Activity

Documentation Summary

---

Export

Formats:

xlsx

csv

pdf

---

Services

ReportGeneratorService

Methods:

generateAttendanceReport()

generateDocumentationReport()

---

Jobs

GenerateReportJob

Background Process.

---

16. Notification Module

Objective

Memberikan notifikasi.

---

Notification Types

Attendance Reminder

Correction Approved

Correction Rejected

Report Generated

---

Channels

Phase 1:

- Database

Phase 2:

- Email

Phase 3:

- Push Notification

---

17. Audit Module

Objective

Mencatat seluruh aktivitas.

---

Events

AttendanceCreated

AttendanceUpdated

DocumentationCreated

CorrectionApproved

---

Listener

CreateAuditLogListener

Actions:

Store User

Store Event

Store Changes

---

18. API Module

Authentication

POST /api/v1/login

---

Attendance

POST /api/v1/attendance/checkin

POST /api/v1/attendance/checkout

GET /api/v1/attendance/history

---

Documentation

POST /api/v1/documentation

GET /api/v1/documentation

---

Reporting

GET /api/v1/reports

---

19. Security Requirements

Mandatory:

- HTTPS
- Sanctum Authentication
- Rate Limiting
- CSRF Protection
- XSS Protection
- Audit Logging

---

Upload Security

Allowed:

jpg

jpeg

png

webp

Rejected:

exe

js

php

sh

---

20. Queue Architecture

Queues:

default

reports

images

notifications

---

Workers

Production:

php artisan horizon

---

21. Scheduled Tasks

Every Day:

attendance:summary

---

Every Night:

backup:run

---

Every Hour:

queue:retry

---

22. Performance Requirements

Attendance Check In:

< 2 seconds

---

Dashboard Load:

< 3 seconds

---

Report Generation:

< 60 seconds

---

23. Testing Requirements

Coverage Target:

80%

---

Required Tests:

Unit Test

- Services
- Repositories

Feature Test

- Authentication
- Attendance
- Documentation

Browser Test

- Check In
- Check Out

---

24. Definition of Done

Suatu fitur dianggap selesai apabila:

✓ Migration selesai

✓ Model selesai

✓ Repository selesai

✓ Service selesai

✓ Policy selesai

✓ Filament Resource selesai

✓ API selesai

✓ Unit Test selesai

✓ Feature Test selesai

✓ Dokumentasi diperbarui

---

25. MVP Deliverables

Release 1.0

✓ Authentication

✓ Employee Management

✓ Branch Management

✓ Division Management

✓ Position Management

✓ Work Location Management

✓ Attendance Check In

✓ Attendance Check Out

✓ Attendance Correction

✓ Documentation

✓ Dashboard

✓ Reporting

✓ Notification

✓ Audit Trail

✓ Queue Processing

✓ Backup Ready

---

Approval

Document Owner:
Technical Architect

Reviewed By:
Lead Developer

Approved By:
Project Manager