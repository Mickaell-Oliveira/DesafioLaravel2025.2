@extends('adminlte::page')

@section('title', $product->name)

@section('content_header')
    <h1>{{ $product->name }}</h1>
@stop

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header text-center">
            <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->name }}" class="img-fluid rounded" style="max-height: 300px;">
        </div>
        <div class="card-body">
            <h3 class="card-title">{{ $product->name }}</h3>
            <p class="card-text">{{ $product->description }}</p>
            <p class="card-text"><strong>Preço:</strong> R$ {{ $product->price }}</p>
            <p class="card-text"><strong>Quantidade disponível:</strong> {{ $product->quantity }}</p>
            <p class="card-text"><strong>Categoria:</strong> {{ $product->category->name }}</p>
            <p class="card-text"><strong>Anunciado por:</strong> {{ $product->seller->name  }}</p>
            <p class="card-text"><strong>Telefone:</strong> {{ $product->seller->phone }}</p>
            @auth
                @if (auth()->user()->type === 'user')
                    <button type="button" class="btn btn-success btn-block mt-3">Comprar</button>
                @endif
            @endauth
        </div>
    </div>
@stop
