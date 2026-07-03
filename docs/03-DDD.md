DATABASE DESIGN DOCUMENT (DDD)

Field Attendance Management System (FAMS)

Version: 1.1
Status: Final
Date: July 2026

---

1. Introduction

Purpose

Dokumen ini mendefinisikan desain database untuk Field Attendance Management System (FAMS).

Dokumen menjadi referensi utama untuk:

- Backend Developer
- Database Engineer
- DevOps Engineer
- QA Engineer
- AI Coding Agent

---

2. Database Technology

Primary Database

PostgreSQL 17

Geospatial Extension

PostGIS

Character Set

UTF-8

Primary Key Strategy

UUID v7

Alasan:

- Aman untuk public exposure
- Mendukung distributed system
- Performa index lebih baik dibanding UUID v4

---

3. Database Design Principles

Mandatory Rules

- UUID Primary Key
- Foreign Key Constraint
- Soft Delete
- Audit Trail
- Created By
- Updated By

---

4. Database Schema Overview

Authentication Domain
│
├── users
├── roles
├── permissions
├── model_has_roles
├── model_has_permissions
└── role_has_permissions

Organization Domain
│
├── branches
├── divisions
├── departments
├── positions
└── employees

Attendance Domain
│
├── work_locations
├── geofences
├── attendance_schedules
├── attendances
├── attendance_photos
├── attendance_logs
├── attendance_corrections
└── attendance_correction_attachments

Documentation Domain
│
├── field_documentations
├── documentation_photos
└── documentation_categories

Reporting Domain
│
├── generated_reports
└── report_exports

System Domain
│
├── audit_logs
├── activity_logs
├── notifications
├── failed_jobs
├── jobs
└── cache

Storage Domain
│
├── media_files
└── media_folders

---

5. Authentication Domain

users

Field| Type
id| uuid
employee_id| uuid
email| varchar(255)
password| varchar(255)
email_verified_at| timestamp
last_login_at| timestamp
created_at| timestamp
updated_at| timestamp
deleted_at| timestamp

Indexes:

- email unique

---

roles

Field| Type
id| uuid
name| varchar(100)
guard_name| varchar(50)

---

permissions

Field| Type
id| uuid
name| varchar(100)
guard_name| varchar(50)

---

6. Organization Domain

branches

Cabang perusahaan.

Field| Type
id| uuid
code| varchar(50)
name| varchar(255)
address| text
latitude| decimal
longitude| decimal
active| boolean

---

divisions

Field| Type
id| uuid
branch_id| uuid
name| varchar(255)

---

departments

Field| Type
id| uuid
division_id| uuid
name| varchar(255)

---

positions

Field| Type
id| uuid
department_id| uuid
name| varchar(255)

---

employees

Field| Type
id| uuid
employee_code| varchar(50)
branch_id| uuid
division_id| uuid
department_id| uuid
position_id| uuid
full_name| varchar(255)
phone| varchar(50)
gender| varchar(20)
hire_date| date
status| varchar(50)

Indexes:

- employee_code unique
- full_name

---

7. Attendance Domain

work_locations

Lokasi kerja resmi.

Field| Type
id| uuid
branch_id| uuid
name| varchar(255)
address| text
latitude| decimal
longitude| decimal
radius_meter| integer
active| boolean

---

geofences

Field| Type
id| uuid
work_location_id| uuid
polygon| geometry (nullable — added via migration 2026_07_04_000001)
active| boolean

PostGIS Type:

geometry(POLYGON,4326)

---

attendance_schedules

Field| Type
id| uuid
name| varchar(255)
checkin_start| time
checkin_end| time
checkout_time| time

---

attendances

Tabel utama absensi.

Field| Type
id| uuid
employee_id| uuid
schedule_id| uuid
attendance_date| date
checkin_time| timestamp
checkout_time| timestamp
status| varchar(50)

Indexes:

- employee_id
- attendance_date
- status

---

attendance_logs

Log GPS lengkap.

Field| Type
id| uuid
attendance_id| uuid
event_type| varchar(50)
latitude| decimal
longitude| decimal
accuracy| decimal
distance_meter| decimal
device_info| jsonb
ip_address| varchar(100)
user_agent| text

event_type:

- checkin
- checkout

---

attendance_photos

Field| Type
id| uuid
attendance_id| uuid
photo_type| varchar(50)
media_file_id| uuid

photo_type:

- checkin
- checkout

---

attendance_corrections

Field| Type
id| uuid
attendance_id| uuid
employee_id| uuid
reason| text
correction_type| varchar(50)
status| varchar(50)
approved_by| uuid
approved_at| timestamp

Status:

- pending
- approved
- rejected

---

attendance_correction_attachments

Field| Type
id| uuid
correction_id| uuid
media_file_id| uuid

---

8. Documentation Domain

documentation_categories

Field| Type
id| uuid
name| varchar(255)

---

field_documentations

Field| Type
id| uuid
employee_id| uuid
category_id| uuid
title| varchar(255)
description| text
latitude| decimal
longitude| decimal
documented_at| timestamp

Indexes:

- employee_id
- documented_at

---

documentation_photos

Field| Type
id| uuid
documentation_id| uuid
media_file_id| uuid

---

9. Storage Domain

media_folders

Field| Type
id| uuid
name| varchar(255)
path| varchar(500)

---

media_files

Field| Type
id| uuid
folder_id| uuid
file_name| varchar(255)
original_name| varchar(255)
mime_type| varchar(100)
file_size| bigint
storage_disk| varchar(50)
storage_path| varchar(500)
checksum| varchar(255)
uploaded_by| uuid

---

10. Reporting Domain

generated_reports

Field| Type
id| uuid
report_type| varchar(100)
parameters| jsonb
generated_by| uuid
generated_at| timestamp

---

report_exports

Field| Type
id| uuid
report_id| uuid
media_file_id| uuid

---

11. Audit Domain

audit_logs

Field| Type
id| uuid
user_id| uuid
event_type| varchar(100)
table_name| varchar(100)
record_id| uuid
old_values| jsonb
new_values| jsonb
created_at| timestamp

---

activity_logs

Field| Type
id| uuid
user_id| uuid
description| text
created_at| timestamp

---

12. Notification Domain

notifications

Field| Type
id| uuid
user_id| uuid
title| varchar(255)
message| text
read_at| timestamp

---

13. Queue Tables

jobs

Laravel Queue Jobs.

failed_jobs

Laravel Failed Jobs.

---

14. PostGIS Strategy

Geofence Query

Validasi radius:

SELECT *
FROM work_locations
WHERE ST_DWithin(
    geography(
        ST_SetSRID(
            ST_MakePoint(longitude, latitude),
            4326
        )
    ),
    geography(
        ST_SetSRID(
            ST_MakePoint(:lng, :lat),
            4326
        )
    ),
    radius_meter
);

---

15. Index Strategy

Mandatory Index:

attendances

- employee_id
- attendance_date
- status

attendance_logs

- attendance_id
- event_type

field_documentations

- employee_id
- documented_at

audit_logs

- user_id
- created_at

---

16. Future Expansion Tables

Phase 2

- leave_requests
- leave_types
- work_shifts
- shift_assignments

Phase 3

- gps_tracking_logs
- tracking_sessions

Phase 4

- face_verifications
- biometric_logs

Phase 5

- payroll_periods
- payroll_items
- payroll_transactions

---

17. Estimated Database Size

100 Employees

≈ 2 GB / Tahun

500 Employees

≈ 10 GB / Tahun

1000 Employees

≈ 20 GB / Tahun

Dengan kompresi foto dan MinIO Lifecycle Policy.

---

18. ERD Summary

branches
 └── divisions
      └── departments
           └── positions

employees
 ├── users
 ├── attendances
 │    ├── attendance_logs
 │    ├── attendance_photos
 │    └── attendance_corrections
 │
 └── field_documentations
      └── documentation_photos

media_files
 ├── attendance_photos
 ├── documentation_photos
 └── report_exports

users
 ├── audit_logs
 └── notifications

---

Approval

Document Owner:
Database Architect

Reviewed By:
Lead Backend Engineer

Approved By:
Software Architect