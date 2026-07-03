<?php

namespace App\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Dashboard as BaseDashboard;

class AdminDashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard Admin';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();
        return $user && $user->hasAnyRole(['super_admin', 'hr_admin']);
    }
}
