<?php

namespace Tests\Unit\Services;

use App\Exceptions\NotificationException;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NotificationService();
    }

    public function test_get_notifications_returns_user_notifications(): void
    {
        $user = User::factory()->create();

        DatabaseNotification::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TravelOrderStatusChanged',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
            'data' => ['message' => 'Test notification'],
            'read_at' => null,
        ]);

        DatabaseNotification::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TravelOrderStatusChanged',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
            'data' => ['message' => 'Another notification'],
            'read_at' => null,
        ]);

        $result = $this->service->getNotifications($user->id);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertGreaterThanOrEqual(2, $result->count());

        foreach ($result as $notification) {
            $this->assertEquals($user->id, $notification->notifiable_id);
            $this->assertEquals('App\Models\User', $notification->notifiable_type);
        }
    }

    public function test_get_notifications_ordered_by_created_at_desc(): void
    {
        $user = User::factory()->create();

        $notification1 = DatabaseNotification::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TravelOrderStatusChanged',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
            'data' => ['message' => 'Old notification'],
            'read_at' => null,
            'created_at' => now()->subDays(2),
        ]);

        $notification2 = DatabaseNotification::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TravelOrderStatusChanged',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
            'data' => ['message' => 'New notification'],
            'read_at' => null,
            'created_at' => now(),
        ]);

        $result = $this->service->getNotifications($user->id);

        $this->assertGreaterThanOrEqual(2, $result->count());
        $this->assertEquals($notification2->id, $result->first()->id);
    }

    public function test_mark_as_read_success(): void
    {
        $user = User::factory()->create();

        $notification = DatabaseNotification::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TravelOrderStatusChanged',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
            'data' => ['message' => 'Test notification'],
            'read_at' => null,
        ]);

        $result = $this->service->markAsRead($notification->id, $user->id);

        $this->assertInstanceOf(DatabaseNotification::class, $result);
        $this->assertNotNull($result->read_at);
        $this->assertEquals($notification->id, $result->id);
    }

    public function test_mark_as_read_throws_exception_when_not_found(): void
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('Notificação não encontrada.');

        $this->service->markAsRead('non-existent-id', 1);
    }

    public function test_mark_as_read_throws_exception_when_unauthorized(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $notification = DatabaseNotification::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TravelOrderStatusChanged',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user1->id,
            'data' => ['message' => 'Test notification'],
            'read_at' => null,
        ]);

        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('Notificação não encontrada.');

        $this->service->markAsRead($notification->id, $user2->id);
    }
}

