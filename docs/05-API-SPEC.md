API SPECIFICATION (API-SPEC)

Field Attendance Management System (FAMS)

Version: 1.1
API Version: v1

---

Authentication

Base URL

/api/v1

Authentication Method

Bearer Token (Laravel Sanctum)

---

POST /auth/login

Login pengguna.

Request

{
  "email": "user@example.com",
  "password": "secret"
}

Response

{
  "success": true,
  "token": "token_here",
  "user": {}
}

---

POST /auth/logout

Logout user.

Headers

Authorization: Bearer {token}

Response

{
  "success": true
}

---

Employee API

GET /employees

Menampilkan daftar karyawan.

Query Parameters

page
search
division
status

Response

{
  "data": [],
  "meta": {}
}

---

POST /employees

Membuat karyawan baru.

Request

{
  "employee_code": "EMP001",
  "full_name": "John Doe",
  "phone": "08123456789"
}

---

GET /employees/{id}

Detail karyawan.

---

PUT /employees/{id}

Update karyawan.

---

DELETE /employees/{id}

Soft delete karyawan.

---

Attendance API

POST /attendance/checkin

Check In.

Request

{
  "latitude": -8.098,
  "longitude": 112.167,
  "accuracy": 10.2,
  "photo": "multipart-file",
  "notes": "Mulai bekerja"
}

Response

{
  "success": true,
  "attendance_id": "uuid"
}

---

POST /attendance/checkout

Check Out.

Request

{
  "latitude": -8.098,
  "longitude": 112.167,
  "accuracy": 12.5,
  "photo": "multipart-file",
  "notes": "Pekerjaan selesai"
}

---

GET /attendance/history

Riwayat absensi.

Filters

start_date
end_date

---

Attendance Correction API

POST /attendance-corrections

Ajukan koreksi absensi.

Request

{
  "attendance_id": "uuid",
  "reason": "Lupa melakukan check out"
}

---

POST /attendance-corrections/{id}/approve

Approval supervisor.

---

POST /attendance-corrections/{id}/reject

Penolakan supervisor.

---

Documentation API

GET /documentations

Daftar dokumentasi.

---

POST /documentations

Membuat dokumentasi baru.

Request

{
  "title": "Pengecekan Gudang",
  "description": "Pemeriksaan stok",
  "latitude": -8.123,
  "longitude": 112.234
}

---

POST /documentations/{id}/photos

Upload foto dokumentasi.

Multipart Form Data

photos[]

---

Reporting API

GET /reports/attendance

Generate laporan absensi.

Filters

start_date
end_date
branch_id
division_id

---

GET /reports/documentations

Generate laporan dokumentasi.

---

Dashboard API

GET /dashboard/admin

Response

{
  "employees": 100,
  "attendance_today": 87,
  "late_today": 5,
  "absent_today": 8
}

---

Notification API

GET /notifications

Daftar notifikasi.

---

POST /notifications/{id}/read

Menandai notifikasi telah dibaca.

---

Error Response Standard

{
  "success": false,
  "message": "Validation failed",
  "errors": {}
}

---

Success Response Standard

{
  "success": true,
  "message": "Success",
  "data": {}
}

---

HTTP Status Codes

200 OK
201 Created
400 Bad Request
401 Unauthorized
403 Forbidden
404 Not Found
422 Validation Error
500 Internal Server Error