TESTING STRATEGY DOCUMENT

Field Attendance Management System (FAMS)

Version: 1.1
Status: Final
Date: July 2026

---

1. Purpose

Dokumen ini mendefinisikan strategi pengujian untuk memastikan kualitas, keamanan, performa, dan stabilitas sistem Field Attendance Management System (FAMS).

Dokumen ini digunakan oleh:

- QA Engineer
- Backend Developer
- Frontend Developer
- DevOps Engineer
- Product Owner
- AI Coding Agent

---

2. Testing Objectives

Tujuan utama:

- Memastikan seluruh fitur berjalan sesuai kebutuhan bisnis.
- Memastikan keamanan aplikasi.
- Memastikan integritas data absensi.
- Memastikan performa aplikasi tetap stabil.
- Mengurangi bug sebelum produksi.

---

3. Quality Targets

Code Coverage

Minimum:

80%

Target:

90%

---

Bug Leakage

Maximum:

< 5%

---

Critical Bug

Target:

0

Sebelum release production.

---

4. Testing Pyramid

                Browser Test
                     10%

              Feature Test
                    30%

              Unit Test
                    60%

---

5. Testing Types

Unit Testing

Tujuan:

Menguji fungsi dan business logic secara terisolasi.

Framework:

PHPUnit
Pest PHP

---

Feature Testing

Tujuan:

Menguji alur aplikasi.

Framework:

Pest PHP
Laravel Test Suite

---

Browser Testing

Tujuan:

Menguji interaksi pengguna.

Framework:

Laravel Dusk

---

API Testing

Tujuan:

Memastikan endpoint API bekerja dengan benar.

Framework:

Pest PHP
Postman
Newman

---

Performance Testing

Tujuan:

Mengukur performa sistem.

Tools:

k6
JMeter

---

Security Testing

Tools:

OWASP ZAP
Larastan
Composer Audit

---

6. Unit Testing Strategy

Employee Module

Coverage:

EmployeeService

Test:

createEmployee()

updateEmployee()

deactivateEmployee()

---

EmployeeRepository

Test:

findById()

findByCode()

paginate()

---

Attendance Module

Coverage:

AttendanceService

Test:

checkIn()

checkOut()

getHistory()

---

GeofenceService

Test:

calculateDistance()

validateLocation()

---

AttendanceValidationService

Test:

validateGps()

validatePhoto()

validateGeofence()

---

Documentation Module

Coverage:

DocumentationService

Test:

store()

uploadPhotos()

delete()

---

Reporting Module

Coverage:

ReportGeneratorService

Test:

generateAttendanceReport()

generateDocumentationReport()

---

7. Feature Testing Strategy

Authentication

Login Success

Expected:

200 OK

---

Login Invalid Password

Expected:

401 Unauthorized

---

Logout

Expected:

Token Revoked

---

Employee Module

Create Employee

Expected:

Employee Created

---

Update Employee

Expected:

Employee Updated

---

Archive Employee

Expected:

Soft Deleted

---

Attendance Module

Check In Success

Expected:

Attendance Created

---

Check In Invalid GPS Accuracy

Expected:

Accepted (accuracy dicatat, tidak divalidasi)

---

Check Out Success

Expected:

Attendance Updated

---

Double Check In

Expected:

Rejected

---

Documentation Module

Create Documentation

Expected:

Documentation Created

---

Upload Multiple Photos

Expected:

Upload Success

---

Upload Invalid File

Expected:

Validation Error

---

Attendance Correction Module

Submit Correction

Expected:

Pending

---

Approve Correction

Expected:

Approved

---

Reject Correction

Expected:

Rejected

---

8. Browser Testing Strategy

Framework:

Laravel Dusk

---

Employee Workflow

Login

Navigate Dashboard

Create Employee

Update Employee

Logout

---

Attendance Workflow

Login

Check In

Upload Selfie

Check Out

Verify History

---

Documentation Workflow

Create Documentation

Upload Photos

Save

Verify History

---

9. API Testing Strategy

Authentication API

Endpoints:

POST /api/v1/auth/login

POST /api/v1/auth/logout

---

Attendance API

Endpoints:

POST /api/v1/attendance/checkin

POST /api/v1/attendance/checkout

GET /api/v1/attendance/history

---

Documentation API

Endpoints:

POST /api/v1/documentations

GET /api/v1/documentations

---

Reporting API

Endpoints:

GET /api/v1/reports

---

10. Database Testing

Migration Testing

Verify:

- Tables Created
- Indexes Created
- Foreign Keys Created

---

Soft Delete Testing

Verify:

deleted_at populated

---

Audit Log Testing

Verify:

Audit Record Created

---

11. Security Testing

Authentication Security

Test:

Invalid Login

Expected:

Unauthorized

---

Expired Token

Expected:

Unauthorized

---

Authorization Security

Employee mencoba akses Admin Endpoint.

Expected:

403 Forbidden

---

File Upload Security

Upload:

malware.exe

Expected:

Rejected

---

Upload:

shell.php

Expected:

Rejected

---

Rate Limiting

Expected:

429 Too Many Requests

---

CSRF Protection

Expected:

419 Page Expired

---

12. GPS Testing

Valid GPS

Expected:

Accepted

---

Accuracy > 50 Meter

Expected:

Rejected

---

Outside Geofence

Expected:

Rejected

---

GPS Disabled

Expected:

Rejected

---

13. Load Testing

Scenario 1

Concurrent Users:

100

Expected:

Response < 3 Seconds

---

Scenario 2

Concurrent Users:

500

Expected:

Response < 5 Seconds

---

Scenario 3

Concurrent Users:

1000

Expected:

No Critical Failure

---

14. Stress Testing

Simulate:

5000 Attendance Requests

Expected:

No Data Corruption

---

15. Backup Testing

Verify:

Database Restore

Success

---

File Restore

Success

---

16. Regression Testing

Run Before:

- Every Release
- Every Hotfix
- Every Major Merge

---

Modules:

Authentication

Employee

Attendance

Documentation

Reporting

---

17. User Acceptance Testing (UAT)

HR Admin

Checklist:

✓ Login

✓ Employee CRUD

✓ Reports

---

Supervisor

Checklist:

✓ View Team Attendance

✓ Approve Corrections

✓ Documentation Monitoring

---

Employee

Checklist:

✓ Check In

✓ Check Out

✓ Upload Documentation

✓ View History

---

18. Test Environment

Local

Purpose:

Developer Testing

---

Staging

Purpose:

QA Testing

---

Production

Purpose:

Final Validation

---

19. CI/CD Testing Gates

Before Merge:

Mandatory:

✓ PHPStan

✓ Larastan

✓ Pint

✓ Unit Test

✓ Feature Test

---

Before Production:

Mandatory:

✓ Browser Test

✓ Security Scan

✓ Load Test

✓ UAT Sign Off

---

20. Release Criteria

Release Allowed If:

✓ Coverage ≥ 80%

✓ No Critical Bug

✓ No High Severity Vulnerability

✓ All UAT Passed

✓ Performance Passed

✓ Security Passed

---

21. Defect Severity Levels

Critical

Examples:

- Login Broken
- Attendance Not Saved
- Database Corruption

Target Fix:

< 4 Hours

---

High

Examples:

- Reporting Error
- Approval Workflow Broken

Target Fix:

< 24 Hours

---

Medium

Examples:

- UI Issue
- Minor Validation Issue

Target Fix:

< 72 Hours

---

Low

Examples:

- Typo
- Layout Issue

Target Fix:

Next Release

---

22. Automation Coverage Targets

Unit Test:

90%

Feature Test:

80%

Browser Test:

70%

API Test:

100%

---

23. Deliverables

QA Team harus menghasilkan:

- Test Plan
- Test Case
- Test Report
- Security Report
- Performance Report
- UAT Report

---

Approval

Document Owner:
QA Lead

Reviewed By:
Lead Developer

Approved By:
Project Manager