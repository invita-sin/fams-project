<?php

namespace App\Services;

use App\Models\WorkLocation;
use App\Repositories\WorkLocationRepositoryInterface;

class GeofenceService
{
    public function __construct(
        private readonly WorkLocationRepositoryInterface $workLocationRepository,
    ) {}

    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function isInsideGeofence(float $latitude, float $longitude, WorkLocation $location): bool
    {
        $distance = $this->calculateDistance(
            $latitude, $longitude,
            (float) $location->latitude,
            (float) $location->longitude
        );

        return $distance <= $location->radius_meter;
    }

    public function findNearestLocation(float $latitude, float $longitude): ?WorkLocation
    {
        $locations = $this->workLocationRepository->findAllActive();

        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($locations as $location) {
            $distance = $this->calculateDistance(
                $latitude, $longitude,
                (float) $location->latitude,
                (float) $location->longitude
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $location;
            }
        }

        return $nearest;
    }
}
