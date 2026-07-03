# Testing Rules

Coverage Target:

Minimum 80%

Preferred 90%

Every feature requires:

1. Unit Test
2. Feature Test

Mandatory Test Targets:

Services
Repositories
Policies
Actions

Example:

AttendanceServiceTest

EmployeeServiceTest

ReportGeneratorServiceTest

## Feature Tests

Authentication

Attendance

Documentation

Reporting

Approval

## Browser Tests

Required for:

- Login
- Check In
- Check Out
- Documentation Upload

## Forbidden

Shipping code without tests.

Skipping critical workflow tests.

## CI Rules

Tests must pass before merge.