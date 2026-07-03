# Filament Rules

Version:

Filament 5

## Resource Rules

Each module must have:

- Resource
- Pages
- Forms
- Tables
- Policies

Example:

EmployeeResource

EmployeeResource\Pages

EmployeeResource\Form

EmployeeResource\Table

EmployeePolicy

## Form Rules

Use:

- Sections
- Fieldsets
- Tabs when needed

Validation must mirror FormRequest.

## Table Rules

Use:

- Filters
- Search
- Sort

Mandatory:

- Created At
- Updated At

## Dashboard Rules

Use widgets only.

Do not place business logic inside widgets.

Widgets call Services.

## Forbidden

Querying database directly inside:

- Widget
- Resource
- Table Column Callback

Always use Service Layer.