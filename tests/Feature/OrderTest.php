<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_of_three_units_reduces_stock_from_ten_to_seven(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 25]);

        $this->post(route('pedidos.store'), [
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'status' => 'confirmed',
            'unit_price' => 0.01,
        ])->assertRedirect(route('pedidos.index'))->assertSessionHasNoErrors();

        $this->assertSame(7, $product->fresh()->stock);
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id, 'product_id' => $product->id,
            'quantity' => 3, 'unit_price' => 25, 'status' => 'confirmed',
        ]);
    }

    public function test_insufficient_stock_rejects_order_without_changing_stock(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->from(route('pedidos.create'))->post(route('pedidos.store'), [
            'customer_id' => $customer->id, 'product_id' => $product->id,
            'quantity' => 11, 'status' => 'pending',
        ])->assertRedirect(route('pedidos.create'))->assertSessionHasErrors('quantity');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(10, $product->fresh()->stock);
    }
}
