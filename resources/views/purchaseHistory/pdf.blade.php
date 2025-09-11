<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Compras</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
        h2 { margin-bottom: 0; }
    </style>
</head>
<body>

    <h2>Relatório de Compras</h2>
    <p>Período: {{ request('start_date') ? date('d/m/Y', strtotime(request('start_date'))) : '--' }} a {{ request('end_date') ? date('d/m/Y', strtotime(request('end_date'))) : '--' }}</p>
    <table>
        <thead>
            <tr>
                <th>Data da Compra</th>
                <th>Valor</th>
                <th>Categorias dos produtos</th>
                <th>Comprador</th>
                <th>Vendedor</th>
            </tr>
        </thead>
        <tbody>
        @forelse($purchases as $purchase)
            <tr>
                <td>{{ $purchase->created_at ? $purchase->created_at->format('d/m/Y H:i') : '-' }}</td>
                <td>R$ {{ number_format($purchase->total, 2, ',', '.') }}</td>
                <td>
                    @foreach($purchase->items as $item)
                        {{ $item->product->category->name ?? '-' }}<br>
                    @endforeach
                </td>
                <td>{{ $purchase->buyer->name }}</td>
                <td>{{ $purchase->seller->name }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5">Nenhuma compra encontrada.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
