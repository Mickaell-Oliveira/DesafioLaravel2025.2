<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Vendas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
        h2 { margin-bottom: 0; }
    </style>
</head>
<body>

    <h2>Relatório de Vendas</h2>
    <p>Período: {{ request('start_date') ? date('d/m/Y', strtotime(request('start_date'))) : '--' }} a {{ request('end_date') ? date('d/m/Y', strtotime(request('end_date'))) : '--' }}</p>
    <table>
        <thead>
            <tr>
                <th>Data da Venda</th>
                <th>Valor</th>
                <th>Categorias dos produtos</th>
                <th>Comprador</th>
                <th>Vendedor</th>
            </tr>
        </thead>
        @if(auth()->user()->type === 'admin')
            <tbody>
            @forelse($adminSales as $sale)
                <tr>
                    <td>{{ $sale->created_at ? $sale->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                    <td>
                        @foreach($sale->items as $item)
                            {{ $item->product->category->name ?? '-' }}<br>
                        @endforeach
                    </td>
                    <td>{{ $sale->buyer->name }}</td>
                    <td>
                        @foreach($sale->items as $item)
                            {{ $item->seller->name ?? '-' }}<br>
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Nenhuma venda encontrada.</td>
                </tr>
            @endforelse
            </tbody>
        @else
            <tbody>
            @forelse($sales as $sale)
                <tr>
                    <td>{{ $sale->created_at ? $sale->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                    <td>
                        @foreach($sale->items as $item)
                            {{ $item->product->category->name ?? '-' }}<br>
                        @endforeach
                    </td>
                    <td>{{ $sale->buyer->name }}</td>
                    <td>{{ auth()->user()->name }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Nenhuma venda encontrada.</td>
                </tr>
            @endforelse
            </tbody>
        @endif
    </table>
</body>
</html>
