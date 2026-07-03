<?php

namespace App\Services;

use App\DTOs\ChangePasswordData;
use App\DTOs\LoginData;
use App\DTOs\UpdateProfileData;
use App\Exceptions\Domain\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthenticationService
{
    public function login(LoginData $data): array
    {
        $user = User::where('email', $data->email)->first();

        if (!$user || !Hash::check($data->password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        $token = $user->createToken('api-token')->plainTextToken;

        $user->update(['last_login_at' => now()]);

        return [
            'token' => $token,
            'user' => $user->only(['id', 'name', 'email']),
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function getProfile(User $user): User
    {
        return $user->load('employee');
    }

    public function updateProfile(User $user, UpdateProfileData $data): User
    {
        $updates = array_filter($data->toArray(), fn ($value) => $value !== null);

        if (!empty($updates)) {
            $user->update($updates);
        }

        return $user->fresh();
    }

    public function changePassword(User $user, ChangePasswordData $data): void
    {
        $user->update([
            'password' => Hash::make($data->newPassword),
        ]);
    }

    public function sendResetLink(string $email): string
    {
        $status = Password::sendResetLink(['email' => $email]);

        return $status;
    }

    public function resetPassword(array $credentials): string
    {
        $status = Password::reset(
            $credentials,
            function (User $user, string $password) {
                $user->update(['password' => Hash::make($password)]);
                $user->tokens()->delete();
            }
        );

        return $status;
    }
}
