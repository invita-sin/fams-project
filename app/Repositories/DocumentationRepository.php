<?php

namespace App\Repositories;

use App\Models\FieldDocumentation;
use Illuminate\Pagination\LengthAwarePaginator;

class DocumentationRepository implements DocumentationRepositoryInterface
{
    public function findById(string $id): ?FieldDocumentation
    {
        return FieldDocumentation::with(['employee', 'photos.mediaFile'])->find($id);
    }

    public function paginate(?string $employeeId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = FieldDocumentation::with(['photos.mediaFile']);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if (!empty($filters['start_date'])) {
            $query->where('activity_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->where('activity_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        return $query->orderBy('activity_date', 'desc')->paginate($perPage);
    }

    public function create(array $data): FieldDocumentation
    {
        return FieldDocumentation::create($data);
    }

    public function update(FieldDocumentation $documentation, array $data): FieldDocumentation
    {
        $documentation->update($data);
        return $documentation->fresh();
    }

    public function delete(FieldDocumentation $documentation): void
    {
        $documentation->delete();
    }
}
