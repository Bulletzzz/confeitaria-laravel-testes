@extends('layout') @section('content')<h1>Novo pedido</h1><form data-cy="order-form" method="POST" action="{{ route('pedidos.store') }}">@include('orders.form')</form>@endsection
