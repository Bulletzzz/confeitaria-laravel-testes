<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('products.index', ['products' => Product::latest()->paginate(10)]);
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Product::create($request->validate([
            'name' => ['required', 'string', 'max:120'], 'description' => ['nullable', 'string'],
            'category' => ['required', 'in:Bolos,Doces,Salgados,Bebidas'], 'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'], 'minimum_stock' => ['required', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ]) + ['active' => $request->boolean('active')]);

        return to_route('produtos.index')->with('success', 'Produto cadastrado com sucesso.');
    }

    public function edit(Product $produto): View
    {
        return view('products.edit', ['product' => $produto]);
    }

    public function update(Request $request, Product $produto): RedirectResponse
    {
        $produto->update($request->validate([
            'name' => ['required', 'string', 'max:120'], 'description' => ['nullable', 'string'],
            'category' => ['required', 'in:Bolos,Doces,Salgados,Bebidas'], 'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'], 'minimum_stock' => ['required', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ]) + ['active' => $request->boolean('active')]);

        return to_route('produtos.index')->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(Product $produto): RedirectResponse
    {
        $produto->delete();

        return to_route('produtos.index')->with('success', 'Produto removido.');
    }
}
