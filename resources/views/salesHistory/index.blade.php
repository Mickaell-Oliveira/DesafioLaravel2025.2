@extends('adminlte::page')

@section('title', 'Histórico de Vendas')

@section('content_header')
    <h1>Histórico de Vendas</h1>
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
                    <a href="{{ route('salesHistory.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-danger" target="_blank">Gerar PDF</a>
                </div>
            </form>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Nome dos Produtos</th>
                        <th>Foto</th>
                        <th>Data da Venda</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>
                                @foreach($sale->items as $item)
                                    {{ $item->product->name }} <br>
                                @endforeach
                            </td>
                            <td>
                                @if($sale->items->first() && $sale->items->first()->product && $sale->items->first()->product->photo)
                                    <img src="{{ asset('storage/' . $sale->items->first()->product->photo) }}" alt="Foto do produto" width="80">
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $sale->created_at ? $sale->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td>R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Nenhuma venda encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $sales->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

        @auth
        @if(auth()->user()->type === 'user')
            <div class="card mt-4">
                <div class="card-body">
                    <h4 class="mb-3">Vendas por mês</h4>
                    {!! $SalesChart->renderHtml() !!}
                </div>
            </div>
            {!! $SalesChart->renderChartJsLibrary() !!}
            {!! $SalesChart->renderJs() !!}
        @endif
    @endauth
@stop
