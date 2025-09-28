<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseHistoryController extends Controller
{
    public function purchaseHistory(Request $request) // exibe o histórico de compras
    {
        $query = Order::where('buyer_id', Auth::id()) // filtra pelo usuário autenticado
            ->with(['buyer', 'items.seller', 'items.product.category' , 'items.product']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date); // filtra pela data de início
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date); // filtra pela data de fim
        }

        $purchases = $query->orderBy('created_at', 'desc')->paginate(10); // ordena por data de criação

        return view('purchaseHistory.index', compact('purchases'));
    }

    public function purchasePdf(Request $request) // gera o PDF do histórico de compras
    {
        $query = Order::where('buyer_id', Auth::id()) // filtra pelo usuário autenticado
            ->with(['buyer', 'items.seller', 'items.product.category' , 'items.product']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date); // filtra pela data de início
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date); // filtra pela data de fim
        }

        $purchases = $query->orderBy('created_at', 'desc')->get(); // obtém todas as compras

        $pdf = Pdf::loadView('purchaseHistory.pdf', compact('purchases')); // carrega a view do PDF
        return $pdf->stream('purchaseHistory.pdf');
    }
}
