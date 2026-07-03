DEPLOYMENT & DEVOPS GUIDE

Field Attendance Management System (FAMS)

Version: 1.1
Status: Production Ready
Date: June 2026

---

1. Purpose

Dokumen ini mendefinisikan standar deployment, infrastruktur, DevOps, CI/CD, monitoring, backup, dan operational procedures untuk Field Attendance Management System (FAMS).

Dokumen digunakan oleh:

- DevOps Engineer
- System Administrator
- Backend Developer
- Software Architect
- QA Engineer

---

2. Deployment Strategy

Environment

Sistem memiliki 3 environment:

local
staging
production

---

Deployment Flow

Developer

↓

Feature Branch

↓

Pull Request

↓

Automated Testing

↓

Develop Branch

↓

Staging Deployment

↓

QA Verification

↓

Main Branch

↓

Production Deployment

---

3. Infrastructure Architecture

Recommended Production Architecture

Internet
    │
    ▼
Cloudflare
    │
    ▼
Nginx Reverse Proxy
    │
    ▼
Laravel Application
    │
 ┌──┼───────────────┐
 ▼  ▼               ▼

Redis PostgreSQL   MinIO

    │
    ▼

Monitoring Stack

---

4. Server Specifications

Small Deployment

100 Employees

4 vCPU
8 GB RAM
100 GB SSD

---

Medium Deployment

500 Employees

8 vCPU
16 GB RAM
200 GB SSD

---

Large Deployment

1000+ Employees

16 vCPU
32 GB RAM
500 GB SSD

---

5. Operating System

Recommended

Ubuntu Server 24.04 LTS

---

Alternatives

Debian 13
Rocky Linux 10

---

6. Containerization Strategy

Production Standard

Docker Compose

---

Future Scale

Kubernetes

---

Containers

nginx

app

scheduler

queue

postgres

redis

minio

---

7. Docker Architecture

docker-compose.yml

services:

- nginx
- app
- queue
- scheduler
- postgres
- redis
- minio

---

8. Application Container

Base Image

php:8.4-fpm

---

Required PHP Extensions

bcmath
ctype
curl
dom
exif
fileinfo
gd
intl
mbstring
openssl
pdo
pdo_pgsql
redis
xml
zip

---

9. Database Container

PostgreSQL

Version:

17

---

Extensions

postgis
uuid-ossp
pgcrypto

---

10. Redis Container

Purpose:

Cache
Queue
Session
Rate Limiting

---

11. MinIO Container

Purpose:

Attendance Photos
Documentation Photos
Reports
Exports

---

Buckets

attendance

documentation

reports

exports

---

12. Nginx Configuration

Public Entry Point

80
443

---

PHP Forwarding

location / {
    try_files $uri $uri/ /index.php?$query_string;
}

---

Security Headers

Mandatory:

X-Frame-Options

X-Content-Type-Options

Referrer-Policy

Content-Security-Policy

---

13. SSL Strategy

Provider

Recommended:

Cloudflare

---

Alternative:

Let's Encrypt

---

SSL Mode

Full Strict

---

14. Environment Variables

Required

APP_NAME=
APP_ENV=
APP_KEY=
APP_URL=

DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

REDIS_HOST=
REDIS_PASSWORD=

FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_BUCKET=
AWS_ENDPOINT=

---

15. Queue Architecture

Horizon

Mandatory.

---

Queues:

default

reports

images

notifications

---

Workers:

2 default

2 images

1 reports

1 notifications

---

16. Scheduler

Production Command

php artisan schedule:work

---

Supervisor Service

Required.

---

17. Logging Strategy

Laravel Log Channel

LOG_CHANNEL=daily

---

Retention:

30 Days

---

Structured Logging

JSON format recommended.

---

18. Monitoring Architecture

Application Monitoring

Laravel Horizon

---

Error Monitoring

Sentry

---

Server Monitoring

Recommended:

Grafana

Prometheus

Node Exporter

---

19. Metrics

Monitor:

CPU

Memory

Disk

Network

Database Connections

Queue Length

Response Time

---

20. Alerting

Critical

CPU:

> 90%

---

Memory:

> 90%

---

Disk:

> 85%

---

Queue Delay:

> 5 Minutes

---

21. Backup Strategy

Database Backup

Frequency:

Daily

Time:

02:00

---

Retention:

30 Days

---

File Backup

Frequency:

Daily

Retention:

30 Days

---

Backup Encryption

AES-256

---

22. Disaster Recovery

Recovery Objectives

RPO

24 Hours

Maximum data loss.

---

RTO

4 Hours

Maximum downtime.

---

23. CI/CD Pipeline

Git Strategy

main

develop

feature/*

---

Pull Request Rules

Required:

✓ Code Review

✓ Automated Tests

✓ Security Scan

---

24. GitHub Actions Pipeline

Workflow

Lint

↓

Static Analysis

↓

Unit Test

↓

Feature Test

↓

Build

↓

Deploy

---

25. Static Analysis

Tools

Laravel Pint

PHPStan

Larastan

---

26. Security Scanning

Dependency Audit

composer audit

---

Additional

Dependabot

OWASP ZAP

---

27. Release Strategy

Development

Automatic Deployment

---

Staging

Automatic Deployment

---

Production

Manual Approval Required

---

28. Database Migration Strategy

Production

php artisan migrate --force

---

Rollback Policy

Forbidden directly on production.

Use:

Hotfix Migration

---

29. Cache Strategy

Drivers

CACHE_STORE=redis

---

Cache Targets

Dashboard Statistics

Reports

Permissions

Configuration

---

30. Session Strategy

Driver

SESSION_DRIVER=redis

---

31. Performance Optimization

Production Commands

php artisan optimize

php artisan config:cache

php artisan route:cache

php artisan event:cache

---

32. Scaling Strategy

Phase 1

Single VPS

---

Phase 2

Dedicated Database

---

Phase 3

Load Balancer

Multiple App Servers

---

Phase 4

Kubernetes Cluster

---

33. Production Hardening Checklist

Before Go Live

✓ HTTPS Enabled

✓ Firewall Configured

✓ SSH Key Only

✓ Root Login Disabled

✓ Fail2Ban Enabled

✓ Redis Password Enabled

✓ PostgreSQL Remote Access Disabled

✓ Backups Tested

✓ Monitoring Enabled

✓ Sentry Enabled

✓ Horizon Enabled

✓ Environment Variables Secured

✓ Queue Workers Running

✓ Scheduled Jobs Running

---

34. Maintenance Procedures

Weekly:

✓ Review Logs

✓ Review Queue Health

✓ Verify Backups

---

Monthly:

✓ Security Updates

✓ Dependency Updates

✓ Disaster Recovery Test

---

Quarterly:

✓ Penetration Testing

✓ Load Testing

✓ Security Audit

---

35. Cost Estimation

MVP

100 Employees

Approximation:

US$10–20/month

Single VPS.

---

Production

500 Employees

Approximation:

US$40–80/month

Dedicated Infrastructure.

---

Enterprise

1000+ Employees

Approximation:

US$150–300/month

HA Architecture.

---

36. Go-Live Checklist

✓ PRD Approved

✓ SAD Approved

✓ DDD Approved

✓ TBS Approved

✓ Security Passed

✓ Testing Passed

✓ UAT Signed Off

✓ Backup Verified

✓ Monitoring Enabled

✓ Deployment Verified

✓ Production Smoke Test Passed

---

Approval

Document Owner:
DevOps Architect

Reviewed By:
Software Architect

Approved By:
Project Sponsor