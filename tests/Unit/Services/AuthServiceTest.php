<?php

namespace Tests\Unit\Services;

use App\Data\User\RegisterDTO;
use App\Exceptions\AuthenticationException;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AuthService();
    }

    public function test_register_creates_user_with_hashed_password(): void
    {
        $dto = new RegisterDTO([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'is_admin' => false,
        ]);

        $user = $this->service->register($dto);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertFalse($user->is_admin);

        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_register_with_admin_flag(): void
    {
        $dto = new RegisterDTO([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'admin123',
            'is_admin' => true,
        ]);

        $user = $this->service->register($dto);

        $this->assertInstanceOf(User::class, $user);
        $this->assertTrue($user->is_admin);
        $this->assertEquals('Admin User', $user->name);
    }

    public function test_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $token = $this->service->login('test@example.com', 'password123');

        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    public function test_login_throws_exception_with_invalid_credentials(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Credenciais inválidas. Verifique seu email e senha.');

        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->service->login('test@example.com', 'wrongpassword');
    }

    public function test_logout_calls_auth_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $token = $this->service->login('test@example.com', 'password123');

        Auth::guard('api')->setToken($token);

        $this->service->logout();
        $this->assertNull(auth('api')->user());
    }

    public function test_me_returns_authenticated_user(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $token = $this->service->login('test@example.com', 'password123');

        Auth::guard('api')->setToken($token);

        $result = $this->service->me();

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
        $this->assertEquals('test@example.com', $result->email);
    }
}

