# Database Rules

Database:

PostgreSQL 17 (production)
MariaDB/MySQL 8 (development)

Production Extensions:

- PostGIS
- pgcrypto

Primary Keys:

UUID v7

Mandatory:

- Foreign Keys
- Indexes
- Audit Fields

Every table must contain:

created_at
updated_at

Use soft delete when defined in DDD.

Use PostGIS for:

- Geofence Validation
- Distance Calculation

Never store business logic inside database triggers unless approved.