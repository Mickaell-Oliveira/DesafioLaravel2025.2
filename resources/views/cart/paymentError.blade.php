@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Erro no Pagamento</h1>
        <p>Ocorreu um erro ao processar seu pagamento. Por favor, tente novamente mais tarde.</p>
    </div>
    <div>
        <a href="{{ route('cart.index') }}">Voltar ao Carrinho</a>
    </div>
@endsection
