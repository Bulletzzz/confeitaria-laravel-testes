<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryController extends Controller
{
    public function index(): View
    {
        return view('deliveries.index', ['deliveries' => Delivery::with('order.customer')->orderByDesc('id')->paginate(10)]);
    }

    public function create(): View
    {
        return view('deliveries.create', ['orders' => Order::with('customer')->whereDoesntHave('delivery')->latest()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Delivery::create($request->validate(['order_id' => ['required', 'exists:orders,id', 'unique:deliveries,order_id'], 'address' => ['required', 'string', 'max:255'], 'city' => ['required', 'string', 'max:80'], 'fee' => ['required', 'numeric', 'min:0'], 'scheduled_at' => ['nullable', 'date'], 'status' => ['required', 'in:scheduled,in_transit,delivered,cancelled'], 'tracking_code' => ['nullable', 'string', 'max:50']]));

        return to_route('entregas.index')->with('success', 'Entrega cadastrada.');
    }

    public function edit(Delivery $entrega): View
    {
        return view('deliveries.edit', ['delivery' => $entrega->load('order.customer'), 'orders' => Order::with('customer')->orderByDesc('id')->get()]);
    }

    public function update(Request $request, Delivery $entrega): RedirectResponse
    {
        $entrega->update($request->validate(['order_id' => ['required', 'exists:orders,id', 'unique:deliveries,order_id,'.$entrega->id], 'address' => ['required', 'string', 'max:255'], 'city' => ['required', 'string', 'max:80'], 'fee' => ['required', 'numeric', 'min:0'], 'scheduled_at' => ['nullable', 'date'], 'status' => ['required', 'in:scheduled,in_transit,delivered,cancelled'], 'tracking_code' => ['nullable', 'string', 'max:50']]));

        return to_route('entregas.index')->with('success', 'Entrega atualizada.');
    }

    public function destroy(Delivery $entrega): RedirectResponse
    {
        $entrega->delete();

        return to_route('entregas.index')->with('success', 'Entrega removida.');
    }
}
