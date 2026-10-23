<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivery_can_be_scheduled_and_advanced_to_delivered(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $order = Order::create([
            'customer_id' => $customer->id, 'product_id' => $product->id,
            'quantity' => 1, 'unit_price' => $product->price, 'status' => 'confirmed',
        ]);
        $data = [
            'order_id' => $order->id, 'address' => 'Rua das Flores, 100', 'city' => 'São Paulo',
            'fee' => 12.50, 'scheduled_at' => '2026-12-01', 'status' => 'scheduled',
            'tracking_code' => 'DOCE123',
        ];

        $this->post(route('entregas.store'), $data)
            ->assertRedirect(route('entregas.index'))->assertSessionHasNoErrors();

        $delivery = $order->delivery()->firstOrFail();
        foreach (['in_transit', 'delivered'] as $status) {
            $this->put(route('entregas.update', $delivery), array_replace($data, ['status' => $status]))
                ->assertRedirect(route('entregas.index'))->assertSessionHasNoErrors();
            $this->assertDatabaseHas('deliveries', ['id' => $delivery->id, 'status' => $status]);
            $this->get(route('entregas.index'))->assertOk()->assertSee($status);
        }
    }
}
