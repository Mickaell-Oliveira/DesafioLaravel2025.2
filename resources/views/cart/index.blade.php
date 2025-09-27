@extends('adminlte::page')

@section('title', 'Carrinho de Compras')

@section('content_header')
    <h1>Meu Carrinho de Compras</h1>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div id="alert-message-success" class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div id="alert-message-error" class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if(!empty($cartItems) && count($cartItems) > 0)
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Produto</th>
                            <th>Preço</th>
                            <th width="150px">Quantidade</th>
                            <th>Subtotal</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $id => $item)
                            <tr>
                                <td>
                                    <img src="{{ asset('storage/' . $item['photo']) }}" width="80" alt="{{ $item['name'] }}">
                                </td>
                                <td>{{ $item['name'] }}</td>
                                <td>R$ {{ formatPrice($item['price']) }}</td>
                                <td>
                                    <form action="{{ route('cart.update', $id) }}" method="POST" class="d-flex">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" class="form-control form-control-sm text-center" min="1">
                                        <button type="submit" class="btn btn-sm btn-primary ms-2" title="Atualizar"><i class="fa fa-sync"></i></button>
                                    </form>
                                </td>
                                <td>R$ {{ formatPrice($item['price'] * $item['quantity']) }}</td>
                                <td>
                                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Remover"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4 text-right">
                    <h3>Total: R$ {{ formatPrice(cartTotal($cartItems)) }}</h3>
                    <a href="{{ url('/') }}" class="btn btn-secondary">Continuar Comprando</a>
                    <form action="{{ route('cart.checkout') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="cart_items" value="{{ json_encode($cartItems) }}">
                        <button type="submit" class="btn btn-success">Finalizar Compra</button>
                    </form>
                </div>
            @else
                <div class="alert alert-info text-center">
                    Seu carrinho está vazio.
                </div>
                <div class="text-center">
                    <a href="{{ url('/') }}" class="btn btn-primary">Ver Produtos</a>
                </div>
            @endif
        </div>
    </div>
<script>
// Sumir com o alerta do carrinho
setTimeout(() => {
    let successAlert = document.getElementById('alert-message-success');
    let errorAlert = document.getElementById('alert-message-error');
    [successAlert, errorAlert].forEach(alert => {
        if (alert) {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        }
    });
}, 1700);
</script>
@stop
