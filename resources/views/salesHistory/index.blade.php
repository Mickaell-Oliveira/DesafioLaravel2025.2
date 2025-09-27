@extends('adminlte::page')

@section('title', 'Histórico de Vendas')

@section('content_header')
    <h1>Histórico de Vendas</h1>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
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
                @if (auth()->user()->type === 'admin')
                <tbody>
                    @forelse($adminSales as $sale)
                        <tr>
                            <td data-label="Nome dos Produtos">
                                @foreach($sale->items as $item)
                                    {{ $item->product->name }}<br>
                                @endforeach
                            </td>
                            <td data-label="Foto">
                                @foreach($sale->items as $item)
                                    @if($item->product && $item->product->photo)
                                        <img src="{{ asset('storage/' . $item->product->photo) }}" alt="Foto do produto" width="80" style="margin-bottom: 4px;">
                                    @else
                                        -
                                    @endif
                                    <br>
                                @endforeach
                            </td>
                            <td data-label="Data da Venda">{{ $sale->created_at ? $sale->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td data-label="Valor">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Nenhuma venda encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-center">
                            {{ $adminSales->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                        </td>
                    </tr>
                </tfoot>

                @else
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td data-label="Nome dos Produto ">
                                @foreach($sale->items as $item)
                                    <td>{{ $item->product->name }} ({{ $item->quantity }}) <br></td>
                                @endforeach
                            </td>
                            <td data-label="Foto">
                                @foreach($sale->items as $item)
                                    <td>
                                        @if($item->product && $item->product->photo)
                                            <img src="{{ asset('storage/' . $item->product->photo) }}" alt="Foto do produto" width="80" style="margin-bottom: 4px;">
                                            <br>
                                            @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                            </td>
                            <td data-label="Data da Venda">{{ $sale->created_at ? $sale->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td data-label="Valor">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Nenhuma venda encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-center">
                            {{ $sales->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            @endif
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
