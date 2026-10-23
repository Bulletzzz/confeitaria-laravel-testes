<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Customer::firstOrCreate(['email' => 'mariana@example.com'], ['name' => 'Mariana Souza', 'phone' => '(11) 99999-1111', 'address' => 'Rua das Flores, 100', 'city' => 'São Paulo']);
        Customer::firstOrCreate(['email' => 'joao@example.com'], ['name' => 'João Oliveira', 'phone' => '(11) 98888-2222', 'address' => 'Av. Central, 250', 'city' => 'São Paulo']);
        Product::firstOrCreate(['name' => 'Bolo de chocolate'], ['description' => 'Bolo recheado para festas.', 'category' => 'Bolos', 'price' => 85, 'stock' => 12, 'minimum_stock' => 3, 'active' => true]);
        Product::firstOrCreate(['name' => 'Brigadeiro gourmet'], ['description' => 'Caixa com 12 unidades.', 'category' => 'Doces', 'price' => 32, 'stock' => 30, 'minimum_stock' => 10, 'active' => true]);
        Product::firstOrCreate(['name' => 'Coxinha cremosa'], ['description' => 'Salgados assados ou fritos.', 'category' => 'Salgados', 'price' => 6.5, 'stock' => 45, 'minimum_stock' => 15, 'active' => true]);
    }
}
