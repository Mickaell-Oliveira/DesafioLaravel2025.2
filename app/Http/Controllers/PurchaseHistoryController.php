<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseHistoryController extends Controller
{
    public function purchaseHistory(Request $request)
    {
        $query = Order::where('buyer_id', Auth::id())
            ->with(['buyer', 'seller', 'items.product.category' , 'items.product']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $purchases = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('purchaseHistory.index', compact('purchases'));
    }

    public function purchasePdf(Request $request)
    {
        $query = Order::where('buyer_id', Auth::id())
            ->with(['buyer', 'seller', 'items.product.category' , 'items.product']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $purchases = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('purchaseHistory.pdf', compact('purchases'));
        return $pdf->stream('purchaseHistory.pdf');
    }
}
