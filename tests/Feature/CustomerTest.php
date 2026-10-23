<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_customer_is_created_via_post(): void
    {
        $data = Customer::factory()->raw();

        $this->post(route('clientes.store'), $data)
            ->assertRedirect(route('clientes.index'))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('customers', $data);
    }

    public function test_customer_contact_and_address_can_be_updated(): void
    {
        $customer = Customer::factory()->create();
        $data = Customer::factory()->raw(['phone' => '(11) 98888-5555', 'address' => 'Rua Nova, 200']);

        $this->put(route('clientes.update', $customer), $data)
            ->assertRedirect(route('clientes.index'))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('customers', ['id' => $customer->id] + $data);
    }

    public function test_customer_can_be_deleted(): void
    {
        $customer = Customer::factory()->create();

        $this->delete(route('clientes.destroy', $customer))->assertRedirect(route('clientes.index'));
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }
}
