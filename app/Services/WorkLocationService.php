<?php

namespace App\Services;

use App\DTOs\CreateWorkLocationData;
use App\DTOs\UpdateWorkLocationData;
use App\Models\WorkLocation;
use App\Repositories\WorkLocationRepositoryInterface;

class WorkLocationService
{
    public function __construct(
        private readonly WorkLocationRepositoryInterface $workLocationRepository,
    ) {}

    public function getAllWorkLocations(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->workLocationRepository->all();
    }

    public function createWorkLocation(CreateWorkLocationData $data): WorkLocation
    {
        return $this->workLocationRepository->create($data->toArray());
    }

    public function updateWorkLocation(string $id, UpdateWorkLocationData $data): WorkLocation
    {
        $location = $this->workLocationRepository->findById($id);

        if (!$location) {
            throw new \RuntimeException('Work location not found');
        }

        return $this->workLocationRepository->update($location, $data->toArray());
    }

    public function getWorkLocation(string $id): ?WorkLocation
    {
        return $this->workLocationRepository->findById($id);
    }

    public function deleteWorkLocation(string $id): void
    {
        $location = $this->workLocationRepository->findById($id);

        if (!$location) {
            throw new \RuntimeException('Work location not found');
        }

        $this->workLocationRepository->delete($location);
    }
}
