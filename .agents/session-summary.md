# FAMS MVP v1.0 — Session Summary

## Goal
Complete MVP v1.0 of Field Attendance Management System (FAMS) — GPS-based attendance, photo documentation, approval workflow, reporting, admin dashboard, audit trail, export/import.

## Progress

### Sprint 1-5 (Original)
- Foundation: Migrations, Models, HasUuid, Auth, Filament Resources, Seeders
- Employee & Attendance Core: Models, Repositories, DTOs, Services, Controllers, Routes
- Features: Field Documentation, Profile, Approval/Correction, Reporting, Dashboard, Schedule, Notifications
- MVP: Export, Photo Upload, Audit Trail, ReportResource, Auto-Absent, Queue, Import, Policies, Rate Limiting, PDF Export, Dashboard API, Notification API, Backup, Employee/Supervisor Dashboard

### Sprint 6 (Optimization & Fixes)
- **Security fixes**: ReportController authorized, ForgotPasswordRequest email enumeration removed, open redirect hardened
- **Database**: Added `polygon` column to `geofences`, FK `users.employee_id → employees.id`
- **Performance**: Fixed N+1 in `ReportGeneratorService::employeeActivityReport()`
- **Code quality**: Extracted DashboardController→DashboardService (7 new Domain Exception classes, centralized handler), removed static Eloquent calls from services, ScheduleController/WorkLocationController use services
- **Infrastructure**: JSON response macros (`response()->success()`, `response()->error()`), 5 new factories (AttendanceLog, AttendancePhoto, DocumentationPhoto, Department, Position)
- **Environment (Acode)**: .bashrc/.profile PATH fix, MariaDB auto-start via setsid
- **Filament fixes**: Namespace `Filament\Tables\Actions\*` → `Filament\Actions\*` for v5 compat, ReportResource selectRaw added `employee_id as id`
- **Role-based redirect**: AdminPanelProvider homeUrl by role (employee→/admin/employee-dashboard, supervisor→/admin/supervisor-dashboard)
- **Geofence removed**: delete geofence/GPS accuracy validation — petugas lapangan hanya butuh timestamp lokasi
- **Camera capture**: ganti upload foto → buka kamera langsung dengan timestamp burned-in via canvas
- **Floating button**: Check In/Out sebagai FAB pojok kanan bawah

## Test Results
**123-127 tests pass** (4 pre-existing failures: BackupDatabaseTest + MarkAbsentEmployeesTest — environment/infrastructure)

## Key Decisions
- Sanctum token + Bearer header pattern (not actingAs)
- UUID pivot keys, MySQL 64-char index name workaround
- Event::fake() order (create models first)
- OpenSpout v4 for XLSX/CSV, dompdf v3 for PDF
- no geofence validation — petugas lapangan bebas check-in dari mana saja
- Camera capture with timestamp burned into photo (canvas) instead of file upload
- $this->redirect() after check-in/out (full page reload) to guarantee fresh stats

## Deviations from Original Plan
- PHP 8.3 instead of 8.4 (compatible — actual: 8.4)
- MariaDB instead of PostgreSQL/PostGIS (dev env constraint)
- Database queue instead of Redis (dev)
- Local disk instead of MinIO (dev)
- Geofence validation dihapus untuk petugas lapangan
- Filament 5 (bukan 4), Livewire 3 (bukan 4)
