<?php

namespace Tests\Unit\Services;

use App\Data\TravelOrder\CreateTravelOrderDTO;
use App\Data\TravelOrder\UpdateTravelOrderStatusDTO;
use App\Exceptions\TravelOrderException;
use App\Models\TravelOrder;
use App\Models\User;
use App\Notifications\TravelOrderStatusChanged;
use App\Services\TravelOrderService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Tests\TestCase;

class TravelOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TravelOrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TravelOrderService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_with_valid_data(): void
    {
        $user = User::factory()->create();

        $dto = new CreateTravelOrderDTO([
            'requester_name' => 'John Doe',
            'destination' => 'New York',
            'departure_date' => '2025-12-01',
            'return_date' => '2025-12-10',
        ]);

        $result = $this->service->create($dto, $user->id);

        $this->assertInstanceOf(TravelOrder::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($dto->requesterName, $result->requester_name);
        $this->assertEquals($dto->destination, $result->destination);
        $this->assertEquals('solicitado', $result->status);
    }

    public function test_create_throws_exception_when_return_date_before_departure(): void
    {
        $this->expectException(TravelOrderException::class);
        $this->expectExceptionMessage('Data de retorno deve ser igual ou posterior à data de partida.');

        $dto = new CreateTravelOrderDTO([
            'requester_name' => 'John Doe',
            'destination' => 'New York',
            'departure_date' => '2025-12-10',
            'return_date' => '2025-12-01',
        ]);

        $this->service->create($dto, 1);
    }

    public function test_find_by_id_returns_travel_order(): void
    {
        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'destination' => 'Paris',
        ]);

        $result = $this->service->findById($travelOrder->id);

        $this->assertInstanceOf(TravelOrder::class, $result);
        $this->assertEquals($travelOrder->id, $result->id);
        $this->assertEquals('Paris', $result->destination);
    }

    public function test_find_by_id_with_user_filter(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $user1->id,
            'destination' => 'Tokyo',
        ]);

        $result = $this->service->findById($travelOrder->id, $user1->id);
        $this->assertEquals($travelOrder->id, $result->id);

        $this->expectException(TravelOrderException::class);
        $this->expectExceptionMessage('Pedido de viagem não encontrado.');

        $this->service->findById($travelOrder->id, $user2->id);
    }

    public function test_find_by_id_throws_exception_when_not_found(): void
    {
        $this->expectException(TravelOrderException::class);
        $this->expectExceptionMessage('Pedido de viagem não encontrado.');

        $this->service->findById(99999);
    }

    public function test_find_all_without_filters(): void
    {
        TravelOrder::factory()->count(3)->create();

        $result = $this->service->findAll();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertGreaterThanOrEqual(3, $result->count());
    }

    public function test_find_all_with_status_filter(): void
    {
        TravelOrder::factory()->create(['status' => 'aprovado']);
        TravelOrder::factory()->create(['status' => 'cancelado']);
        TravelOrder::factory()->create(['status' => 'solicitado']);

        $result = $this->service->findAll(['status' => 'aprovado']);

        $this->assertInstanceOf(Collection::class, $result);
        foreach ($result as $order) {
            $this->assertEquals('aprovado', $order->status);
        }
    }

    public function test_find_all_with_destination_filter(): void
    {
        TravelOrder::factory()->create(['destination' => 'New York']);
        TravelOrder::factory()->create(['destination' => 'Paris']);
        TravelOrder::factory()->create(['destination' => 'New Delhi']);

        $result = $this->service->findAll(['destination' => 'New']);

        $this->assertInstanceOf(Collection::class, $result);
        foreach ($result as $order) {
            $this->assertStringContainsString('New', $order->destination);
        }
    }

    public function test_find_all_with_date_filters(): void
    {
        TravelOrder::factory()->create([
            'departure_date' => '2025-12-01',
            'return_date' => '2025-12-10',
        ]);

        TravelOrder::factory()->create([
            'departure_date' => '2025-12-15',
            'return_date' => '2025-12-20',
        ]);

        // Test departure_date filter
        $result = $this->service->findAll(['departure_date' => '2025-12-01']);
        $this->assertGreaterThanOrEqual(1, $result->count());

        // Test date range filters
        $result = $this->service->findAll([
            'departure_date_from' => '2025-12-01',
            'departure_date_to' => '2025-12-10',
        ]);
        $this->assertGreaterThanOrEqual(1, $result->count());
    }

    public function test_update_status_success(): void
    {
        $travelOrder = TravelOrder::factory()->create([
            'status' => 'solicitado',
        ]);

        $dto = new UpdateTravelOrderStatusDTO(['status' => 'aprovado']);

        Notification::fake();

        $result = $this->service->updateStatus($travelOrder->id, $dto);

        $this->assertInstanceOf(TravelOrder::class, $result);
        $this->assertEquals('aprovado', $result->status);
    }

    public function test_update_status_throws_exception_when_not_found(): void
    {
        $this->expectException(TravelOrderException::class);
        $this->expectExceptionMessage('Pedido de viagem não encontrado.');

        $dto = new UpdateTravelOrderStatusDTO(['status' => 'aprovado']);

        $this->service->updateStatus(99999, $dto);
    }

    public function test_update_status_throws_exception_when_already_approved(): void
    {
        $travelOrder = TravelOrder::factory()->create([
            'status' => 'aprovado',
        ]);

        $this->expectException(TravelOrderException::class);
        $this->expectExceptionMessage("Transição de status inválida. O pedido está com status 'aprovado' e não pode ser alterado.");

        $dto = new UpdateTravelOrderStatusDTO(['status' => 'cancelado']);

        $this->service->updateStatus($travelOrder->id, $dto);
    }

    public function test_update_status_throws_exception_when_already_cancelled(): void
    {
        $travelOrder = TravelOrder::factory()->create([
            'status' => 'cancelado',
        ]);

        $this->expectException(TravelOrderException::class);
        $this->expectExceptionMessage("Transição de status inválida. O pedido está com status 'cancelado' e não pode ser alterado.");

        $dto = new UpdateTravelOrderStatusDTO(['status' => 'aprovado']);

        $this->service->updateStatus($travelOrder->id, $dto);
    }

    public function test_update_status_sends_notification_when_approved(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'status' => 'solicitado',
            'destination' => 'Paris',
        ]);

        $dto = new UpdateTravelOrderStatusDTO(['status' => 'aprovado']);

        $this->service->updateStatus($travelOrder->id, $dto);

        Notification::assertSentTo(
            $user,
            TravelOrderStatusChanged::class,
            function ($notification, $channels) use ($travelOrder) {
                $data = $notification->toArray($notification);
                return $data['order_id'] === $travelOrder->id
                    && $data['destination'] === 'Paris'
                    && $data['old_status'] === 'solicitado'
                    && $data['new_status'] === 'aprovado';
            }
        );
    }

    public function test_update_status_sends_notification_when_cancelled(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'status' => 'solicitado',
            'destination' => 'London',
        ]);

        $dto = new UpdateTravelOrderStatusDTO(['status' => 'cancelado']);

        $this->service->updateStatus($travelOrder->id, $dto);

        Notification::assertSentTo(
            $user,
            TravelOrderStatusChanged::class,
            function ($notification, $channels) use ($travelOrder) {
                $data = $notification->toArray($notification);
                return $data['order_id'] === $travelOrder->id
                    && $data['destination'] === 'London'
                    && $data['old_status'] === 'solicitado'
                    && $data['new_status'] === 'cancelado';
            }
        );
    }

    public function test_update_status_does_not_send_notification_for_other_statuses(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'status' => 'solicitado',
        ]);

        $dto = new UpdateTravelOrderStatusDTO(['status' => 'solicitado']);

        $this->service->updateStatus($travelOrder->id, $dto);

        Notification::assertNothingSent();
    }
}

