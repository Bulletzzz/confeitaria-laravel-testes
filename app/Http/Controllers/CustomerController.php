<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        return view('customers.index', ['customers' => Customer::latest()->paginate(10)]);
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Customer::create($request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['nullable', 'email'], 'phone' => ['nullable', 'string', 'max:30'], 'address' => ['nullable', 'string', 'max:255'], 'city' => ['nullable', 'string', 'max:80']]));

        return to_route('clientes.index')->with('success', 'Cliente cadastrado com sucesso.');
    }

    public function edit(Customer $cliente): View
    {
        return view('customers.edit', ['customer' => $cliente]);
    }

    public function update(Request $request, Customer $cliente): RedirectResponse
    {
        $cliente->update($request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['nullable', 'email'], 'phone' => ['nullable', 'string', 'max:30'], 'address' => ['nullable', 'string', 'max:255'], 'city' => ['nullable', 'string', 'max:80']]));

        return to_route('clientes.index')->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Customer $cliente): RedirectResponse
    {
        $cliente->delete();

        return to_route('clientes.index')->with('success', 'Cliente removido.');
    }
}
