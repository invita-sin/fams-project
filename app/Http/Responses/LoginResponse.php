<?php

namespace App\Http\Responses;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = auth()->user();
        $panel = Filament::getCurrentPanel();

        if ($user->hasRole('supervisor')) {
            $route = 'filament.' . $panel->getId() . '.pages.supervisor-dashboard';
        } elseif ($user->hasRole('employee')) {
            $route = 'filament.' . $panel->getId() . '.pages.employee-dashboard';
        } else {
            $route = 'filament.' . $panel->getId() . '.pages.dashboard';
        }

        if (Route::has($route)) {
            return redirect()->route($route);
        }

        return redirect()->intended(Filament::getUrl($panel));
    }
}
