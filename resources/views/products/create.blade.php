@extends('layout') @section('content')<h1>Novo produto</h1><form data-cy="product-form" method="POST" action="{{ route('produtos.store') }}">@include('products.form')</form>@endsection
