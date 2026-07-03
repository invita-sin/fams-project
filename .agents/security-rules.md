# Security Rules

Authentication:

Laravel Sanctum

Authorization:

Spatie Permission

## Mandatory

- CSRF Protection
- XSS Protection
- Rate Limiting
- Audit Logging

## Upload Rules

Allowed:

- jpg
- jpeg
- png
- webp

Forbidden:

- php
- exe
- sh
- js

## GPS Rules

Attendance requires:

- Latitude
- Longitude
- Accuracy

Accuracy > 50m

Reject attendance.

## Secrets

Never hardcode:

- Passwords
- Tokens
- API Keys

Use ENV only.

## Database

Use:

- Eloquent
- Query Builder

Avoid raw SQL unless approved.