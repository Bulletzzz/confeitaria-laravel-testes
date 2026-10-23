<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Doce Gestão' }}</title>
    <style>
        :root{font-family:system-ui,-apple-system,sans-serif;color:#33221f;background:#fffaf7}*{box-sizing:border-box}body{margin:0}nav{background:#6d3d35;color:#fff;padding:1rem 5%;display:flex;gap:1.2rem;align-items:center;flex-wrap:wrap}nav strong{font-size:1.3rem;margin-right:auto}nav a{color:#fff;text-decoration:none}.container{max-width:1100px;margin:2rem auto;padding:0 1rem}h1{color:#6d3d35}.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem}.card,form,.table-wrap{background:#fff;padding:1.25rem;border-radius:12px;box-shadow:0 2px 12px #5b322414;margin-bottom:1rem}.card b{font-size:2rem;color:#b55a4c}table{width:100%;border-collapse:collapse}th,td{text-align:left;padding:.75rem;border-bottom:1px solid #eadbd5}th{color:#6d3d35}label{display:block;font-weight:600;margin:.8rem 0 .25rem}input,select,textarea{width:100%;padding:.65rem;border:1px solid #d8c4bc;border-radius:7px;font:inherit}button,.btn{background:#b55a4c;color:#fff;border:0;border-radius:7px;padding:.65rem 1rem;text-decoration:none;cursor:pointer;display:inline-block}.btn.secondary{background:#805f58}.btn.danger{background:#a43e3e}.actions{display:flex;gap:.5rem;align-items:center;flex-wrap:wrap}.alert{padding:1rem;border-radius:8px;background:#dff2e4;color:#1b6530;margin-bottom:1rem}.error{color:#a43e3e;font-size:.9rem}small.badge{padding:.25rem .5rem;border-radius:1rem;background:#f3e2d6}
    </style>
</head>
<body>
<nav><strong data-cy="brand">🍰 Doce Gestão</strong><a href="{{ url('/') }}">Início</a><a data-cy="nav-products" href="{{ route('produtos.index') }}">Produtos</a><a data-cy="nav-customers" href="{{ route('clientes.index') }}">Clientes</a><a data-cy="nav-orders" href="{{ route('pedidos.index') }}">Pedidos</a><a data-cy="nav-deliveries" href="{{ route('entregas.index') }}">Fretes</a></nav>
<main class="container">
    @if(session('success'))<div data-cy="success-message" class="alert" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div data-cy="validation-errors" class="alert" role="alert" style="background:#ffe5e5;color:#8d2525"><strong>Revise os campos:</strong><ul>@foreach($errors->messages() as $field => $messages)@foreach($messages as $error)<li data-cy="error-{{ $field }}">{{ $error }}</li>@endforeach @endforeach</ul></div>@endif
    @yield('content')
</main>
</body>
</html>
