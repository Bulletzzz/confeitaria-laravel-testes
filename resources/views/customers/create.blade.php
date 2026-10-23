@extends('layout') @section('content')<h1>Novo cliente</h1><form data-cy="customer-form" method="POST" action="{{ route('clientes.store') }}">@include('customers.form')</form>@endsection
