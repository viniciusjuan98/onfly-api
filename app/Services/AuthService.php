<?php

namespace App\Services;

use App\Data\User\RegisterDTO;
use App\Exceptions\AuthenticationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(RegisterDTO $dto): User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'is_admin' => $dto->is_admin
        ]);
    }

    public function login(string $email, string $password): string
    {
        $credentials = ['email' => $email, 'password' => $password];

        if (!$token = auth('api')->attempt($credentials)) {
            throw AuthenticationException::invalidCredentials();
        }

        return $token;
    }

    public function logout(): void
    {
        auth('api')->logout();
    }

    public function me(): ?User
    {
        return auth('api')->user();
    }
}
