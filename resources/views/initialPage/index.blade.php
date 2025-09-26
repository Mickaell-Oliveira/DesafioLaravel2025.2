@extends('adminlte::page')

@section('title', 'Produtos')

@section('content_header')
    <h1>Produtos</h1>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop

@section('content')
    <form action="{{ route('products.index') }}" method="GET" class="mb-4">
        <div class="input-group mb-2">
            <input type="text" name="query" class="form-control" placeholder="Buscar produtos..." value="{{ $query ?? '' }}">
            <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
        <div class="mb-2">
            <div class="btn-group category-filter" role="group">
                @foreach($categories as $category)
                    <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $category->id])) }}"
                       class="btn btn-outline-secondary {{ request('category') == $category->id ? 'active' : '' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
                <a href="{{ route('products.index', request()->except(['category', 'page'])) }}"
                   class="btn btn-outline-secondary {{ !request('category') ? 'active' : '' }}">
                    Todas
                </a>
            </div>
        </div>
    </form>
    @if(session('success'))
        <div id="alert-message-success" class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div>
        @forelse ($products as $product)
            @if(auth()->user()->id !== $product->user_id)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border-bottom">
                    <a href="{{ route('products.show', $product->id) }}" class="text-dark text-decoration-none">
                        <strong>{{ $product->name }}</strong> <br>
                        <span class="text-muted">R$ {{ formatPrice($product->price) }}</span>
                    </a>
                    @if(auth()->user()->type === 'user')
                        @if (verifyStock($product->id, 1))
                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-shopping-cart"></i> Adicionar
                                </button>
                            </form>
                        @else
                            <span class="text-danger">Fora de estoque</span>
                        @endif
                    @endif
                </div>
            @endif
        @empty
            <div class="alert alert-info text-center">Nenhum produto encontrado.</div>
        @endforelse
    </div>
    <div class="mt-4">
        {{ $products->links('pagination::bootstrap-5') }}
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
