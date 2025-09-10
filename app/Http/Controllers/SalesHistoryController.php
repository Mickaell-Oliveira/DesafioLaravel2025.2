<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

class SalesHistoryController extends Controller
{
    public function salesHistory(Request $request)
    {
        $query = Order::where('seller_id', Auth::id())
            ->with(['buyer', 'seller', 'items.product.category']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('salesHistory.index', compact('sales'));
    }

    public function pdf(Request $request)
    {
        $query = Order::where('seller_id', Auth::id())
            ->with(['buyer', 'seller', 'items.product.category']); 

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $sales = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('salesHistory.pdf', compact('sales'));
        return $pdf->stream('salesHistory.pdf');
    }

}
