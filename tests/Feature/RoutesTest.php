<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_displays_doce_gestao(): void
    {
        $this->get('/')->assertOk()->assertSee('Doce Gestão');
    }

    #[DataProvider('moduleRoutes')]
    public function test_module_listing_is_accessible(string $route, string $title): void
    {
        $this->get(route($route))->assertStatus(200)->assertSee($title);
    }

    public static function moduleRoutes(): array
    {
        return [
            'produtos' => ['produtos.index', 'Produtos e estoque'],
            'clientes' => ['clientes.index', 'Clientes'],
            'pedidos' => ['pedidos.index', 'Pedidos / vendas'],
            'fretes' => ['entregas.index', 'Fretes e entregas'],
        ];
    }

    public function test_unknown_route_returns_404(): void
    {
        $this->get('/rota-que-nao-existe')->assertNotFound();
    }
}
