<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_created_product_is_persisted_and_visible(): void
    {
        $product = Product::factory()->create(['name' => 'Bolo de teste']);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Bolo de teste']);
        $this->get(route('produtos.index'))->assertOk()->assertSee('Bolo de teste');
    }

    public function test_valid_product_can_be_created(): void
    {
        $data = Product::factory()->raw();

        $this->post(route('produtos.store'), $data)
            ->assertRedirect(route('produtos.index'))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('products', ['name' => $data['name'], 'stock' => 10]);
    }

    #[DataProvider('invalidProducts')]
    public function test_invalid_product_is_rejected(string $field, mixed $value): void
    {
        $data = Product::factory()->raw([$field => $value]);

        $this->from(route('produtos.create'))->post(route('produtos.store'), $data)
            ->assertRedirect(route('produtos.create'))
            ->assertSessionHasErrors($field);
        $this->assertDatabaseCount('products', 0);
    }

    public static function invalidProducts(): array
    {
        return [
            'nome ausente' => ['name', ''],
            'preço negativo' => ['price', -1],
            'preço em texto' => ['price', 'abc'],
            'estoque negativo' => ['stock', -1],
            'estoque em texto' => ['stock', 'abc'],
            'categoria inválida' => ['category', 'Outra'],
            'estoque mínimo negativo' => ['minimum_stock', -1],
        ];
    }

    public function test_empty_product_reports_all_required_fields(): void
    {
        $this->post(route('produtos.store'), [])
            ->assertSessionHasErrors(['name', 'category', 'price', 'stock', 'minimum_stock']);
        $this->assertDatabaseCount('products', 0);
    }

    public function test_product_price_can_be_updated(): void
    {
        $product = Product::factory()->create(['price' => 20]);

        $this->put(route('produtos.update', $product), Product::factory()->raw(['price' => 49.90]))
            ->assertRedirect(route('produtos.index'))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'price' => 49.90]);
    }

    public function test_product_without_orders_can_be_deleted(): void
    {
        $product = Product::factory()->create();

        $this->delete(route('produtos.destroy', $product))->assertRedirect(route('produtos.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
