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
                <th>Categoria do Produto</th>
                <th>Comprador</th>
                <th>Vendedor</th>
            </tr>
        </thead>
        <tbody>
        @forelse($purchases as $purchase)
            @foreach($purchase->items as $item)
                <tr>
                    <td>{{ $purchase->created_at ? $purchase->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
                    <td>{{ $item->product->category->name ?? '-' }}</td>
                    <td>{{ $purchase->buyer->name ?? '-' }}</td>
                    <td>{{ $item->seller->name ?? '-' }}</td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="5">Nenhuma compra encontrada.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
