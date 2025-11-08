<?php

namespace App\Services;

use App\Data\User\RegisterDTO;
use App\Exceptions\AuthenticationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Info(title="API Documentation", version="1.0.0")
 * @OA\Server(url="http://localhost:8000")
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */
class AuthService
{
    public function register(RegisterDTO $dto): User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password)
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
