@extends('adminlte::page')

@section('title', 'Histórico de Compras')

@section('content_header')
    <h1>Histórico de Compras</h1>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="icon" href="/vendor/adminlte/dist/img/purchaseIcon.jpeg" type="image/jpeg">
@stop

@section('content')
    <div class="card mt-3">
        <div class="card-body">
            <form class="row g-3 mb-4" method="GET" action="">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Data inicial</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Data final</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                    <a href="{{ route('purchaseHistory.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-danger" target="_blank">Gerar PDF</a>
                </div>
            </form>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Nome dos Produtos</th>
                        <th>Foto</th>
                        <th>Data da Compra</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td data-label="Nome dos Produtos">
                                @foreach($purchase->items as $item)
                                    {{ $item->product->name }} ({{ $item->quantity }}) <br>
                                @endforeach
                            </td>
                            <td data-label="Foto">
                                <div class="td-content">
                                    @foreach($purchase->items as $item)

                                            @if ($item->product && $item->product->photo)
                                                <img class="table-img" src="{{ asset('storage/' . $item->product->photo) }}" alt="Foto do produto" width="80">
                                                <br>
                                            @else
                                                - <br>
                                            @endif

                                    @endforeach
                                </div>
                            </td>
                            <td data-label="Data da Compra">{{ $purchase->created_at ? $purchase->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td data-label="Valor">R$ {{ formatPrice($purchase->total) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Nenhuma compra encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $purchases->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@stop
