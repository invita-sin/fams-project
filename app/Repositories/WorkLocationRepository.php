<?php

namespace App\Repositories;

use App\Models\WorkLocation;

class WorkLocationRepository implements WorkLocationRepositoryInterface
{
    public function findById(string $id): ?WorkLocation
    {
        return WorkLocation::with(['branch', 'geofences'])->find($id);
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return WorkLocation::with(['branch', 'geofences'])->orderBy('name')->get();
    }

    public function findAllActive(): \Illuminate\Database\Eloquent\Collection
    {
        return WorkLocation::where('active', true)->get();
    }

    public function create(array $data): WorkLocation
    {
        return WorkLocation::create($data);
    }

    public function update(WorkLocation $location, array $data): WorkLocation
    {
        $location->update($data);

        return $location->fresh()->load(['branch', 'geofences']);
    }

    public function delete(WorkLocation $location): bool
    {
        return $location->delete();
    }
}
