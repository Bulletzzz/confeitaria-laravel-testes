<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        return view('orders.index', ['orders' => Order::with(['customer', 'product'])->orderByDesc('id')->paginate(10)]);
    }

    public function create(): View
    {
        return view('orders.create', ['customers' => Customer::orderBy('name')->get(), 'products' => Product::where('active', true)->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['customer_id' => ['required', 'exists:customers,id'], 'product_id' => ['required', 'exists:products,id'], 'quantity' => ['required', 'integer', 'min:1'], 'status' => ['required', 'in:pending,confirmed,ready,completed,cancelled'], 'notes' => ['nullable', 'string']]);
        $product = Product::findOrFail($data['product_id']);
        if ($product->stock < $data['quantity']) {
            return back()->withInput()->withErrors(['quantity' => 'Estoque insuficiente para este produto.']);
        }
        $data['unit_price'] = $product->price;
        Order::create($data);
        $product->decrement('stock', $data['quantity']);

        return to_route('pedidos.index')->with('success', 'Pedido criado e estoque atualizado.');
    }

    public function edit(Order $pedido): View
    {
        return view('orders.edit', ['order' => $pedido->load(['customer', 'product']), 'customers' => Customer::orderBy('name')->get(), 'products' => Product::orderBy('name')->get()]);
    }

    public function update(Request $request, Order $pedido): RedirectResponse
    {
        $data = $request->validate(['customer_id' => ['required', 'exists:customers,id'], 'product_id' => ['required', 'exists:products,id'], 'quantity' => ['required', 'integer', 'min:1'], 'status' => ['required', 'in:pending,confirmed,ready,completed,cancelled'], 'notes' => ['nullable', 'string']]);
        $pedido->update($data);

        return to_route('pedidos.index')->with('success', 'Pedido atualizado.');
    }

    public function destroy(Order $pedido): RedirectResponse
    {
        $pedido->delete();

        return to_route('pedidos.index')->with('success', 'Pedido removido.');
    }
}
