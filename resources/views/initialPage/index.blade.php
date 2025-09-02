@extends('adminlte::page')

@section('title', 'Produtos')

@section('content_header')
    <h1>Produtos</h1>
@stop

@section('content')
    <form action="{{ route('products.index') }}" method="GET" class="mb-4">
        <div class="input-group mb-2">
            <input type="text" name="query" class="form-control" placeholder="Buscar produtos..." value="{{ $query ?? '' }}">
            <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
        <div class="mb-2">
            <div class="btn-group" role="group">
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
    <div>
        @forelse ($products as $product)
            @auth
                @if(auth()->user()->id !== $product->seller)
                    <div type="button" onclick="window.location='{{ route('products.show', $product->id) }}'" class="mb-2 p-2 border-bottom cursor-pointer">
                        <strong>{{ $product->name }}</strong> <br>
                        <span class="text-muted">R$ {{ $product->price }}</span>
                        @auth
                            @if(auth()->user()->type === 'user')
                                <button type="button" class="btn btn-sm btn-success float-right">Comprar</button>
                            @endif
                        @endauth
                    </div>
                @endif
            @endauth

        @empty
            <div class="text-danger">Nenhum produto encontrado.</div>
        @endforelse
    </div>
    <div class="mt-4">
    {{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
@stop

