<?php

namespace App\Http\Controllers\Api;

use App\DTOs\ChangePasswordData;
use App\DTOs\LoginData;
use App\DTOs\UpdateProfileData;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\AuthenticationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthenticationService $authService,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $data = LoginData::fromRequest($request->validated());
        $result = $this->authService->login($data);

        return response()->json([
            'success' => true,
            'token' => $result['token'],
            'user' => $result['user'],
        ]);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout(request()->user());

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function profile(): JsonResponse
    {
        $user = $this->authService->getProfile(request()->user());

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $data = UpdateProfileData::fromRequest($request->validated());
        $user = $this->authService->updateProfile($request->user(), $data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated',
            'data' => $user,
        ]);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $data = ChangePasswordData::fromRequest($request->validated());
        $this->authService->changePassword($request->user(), $data);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->sendResetLink($request->input('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => __($status),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __($status),
        ], 400);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->resetPassword($request->validated());

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => __($status),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __($status),
        ], 400);
    }
}
