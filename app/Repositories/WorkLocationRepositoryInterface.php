<?php

namespace App\Repositories;

use App\Models\WorkLocation;

interface WorkLocationRepositoryInterface
{
    public function findById(string $id): ?WorkLocation;

    public function all(): \Illuminate\Database\Eloquent\Collection;

    public function findAllActive(): \Illuminate\Database\Eloquent\Collection;

    public function create(array $data): WorkLocation;

    public function update(WorkLocation $location, array $data): WorkLocation;

    public function delete(WorkLocation $location): bool;
}
