<?php

namespace App\Repositories;

use App\Models\FieldDocumentation;
use Illuminate\Pagination\LengthAwarePaginator;

interface DocumentationRepositoryInterface
{
    public function findById(string $id): ?FieldDocumentation;

    public function paginate(?string $employeeId, int $perPage = 10, array $filters = []): LengthAwarePaginator;

    public function create(array $data): FieldDocumentation;

    public function update(FieldDocumentation $documentation, array $data): FieldDocumentation;

    public function delete(FieldDocumentation $documentation): void;
}
