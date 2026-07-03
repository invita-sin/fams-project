<?php

namespace App\Services;

use App\Models\WorkLocation;

class AttendanceValidationService
{
    public function __construct(
        private readonly GeofenceService $geofenceService,
    ) {}

    public function validateGps(float $accuracy): bool
    {
        return $accuracy <= 50;
    }

    public function validateGeofence(float $latitude, float $longitude, WorkLocation $location): bool
    {
        return $this->geofenceService->isInsideGeofence($latitude, $longitude, $location);
    }

    public function validatePhoto(): bool
    {
        return true;
    }
}
