# Coding Standards

## PHP

Follow:

- PSR-12
- Laravel Best Practices

Formatting:

- Typed properties mandatory
- Return types mandatory
- Constructor Property Promotion preferred

Example:

```php
class EmployeeService
{
    public function createEmployee(
        EmployeeData $data
    ): Employee {
        //
    }
}
```

## Forbidden

- Massive Controllers
- Business Logic in Controllers
- Raw SQL without justification
- Facade abuse
- Static helper abuse

## Mandatory

- DTOs
- Services
- Repositories
- Policies
- Form Requests

## Naming

Services:

- EmployeeService
- AttendanceService

Repositories:

- EmployeeRepository
- AttendanceRepository

DTOs:

- CreateEmployeeData
- CheckInData

Actions:

- CreateAttendanceAction
- GenerateReportAction

Jobs:

- GenerateReportJob
- ProcessDocumentationImageJob

Events:

- AttendanceCreated
- DocumentationCreated

Listeners:

- CreateAuditLogListener