@extends('adminlte::page')

@if(session('success'))
    <div id="alert-message-success" class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


@section('title', $product->name)

@section('content_header')
    <h1>{{ $product->name }}</h1>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="icon" href="{{ asset('storage/'. $product->photo) }}" type="image/png">
@stop

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header text-center">
            <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->name }}" class="img-fluid rounded" style="max-height: 300px;">
        </div>
        <div class="card-body">
            <h3 class="card-title">{{ $product->name }}</h3>
            <p class="card-text">{{ $product->description }}</p>
            <p class="card-text"><strong>Preço:</strong> R$ {{ formatPrice($product->price) }}</p>
            <p class="card-text"><strong>Quantidade disponível:</strong> {{ $product->quantity > 0 ? $product->quantity : 'Fora de estoque' }}</p>
            <p class="card-text"><strong>Categoria:</strong> {{ $product->category->name }}</p>
            <p class="card-text"><strong>Anunciado por:</strong> {{ $product->seller->name }}</p>
            <p class="card-text"><strong>Telefone:</strong> {{ $product->seller->phone }}</p>

            @auth
                @if(auth()->user()->type === 'user')
                    @if(verifyStock($product->id,1))
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mt-3">
                            @csrf
                            <div class="input-group">
                                <input type="number" name="quantity" class="form-control" value="1" min="1" max="{{ $product->quantity }}">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-shopping-cart"></i> Adicionar ao Carrinho
                                </button>
                            </div>
                        </form>
                    @endif
                @endif
            @endauth
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
