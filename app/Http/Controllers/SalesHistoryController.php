<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use LaravelDaily\LaravelCharts\Classes\LaravelChart;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class SalesHistoryController extends Controller
{
    public function salesHistory(Request $request)
    {
        $query = Order::whereHas('items.product', function ($subQuery) {
            $subQuery->where('seller_id', Auth::id());
        })->with([
            'buyer',
            'seller',
            'items' => function ($q) {
                $q->whereHas('product', function ($subQuery) {
                    $subQuery->where('seller_id', Auth::id());
                })->with('product.category', 'product');
            }
        ]);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(10);

        $SalesChart_options =[
            'chart_title'           => 'Vendas Realizadas no Mês',
            'model'                 => OrderItem::class,
            'chart_type'            => 'line',
            'report_type'           => 'group_by_date',
            'group_by_field'        => 'created_at',
            'group_by_period'       => 'month',
            'chart_color'           => '0,122,255',
            'filter_field'          => 'created_at',
            'filter_days'           => 365,
            'where_raw'      => 'seller_id = ' . Auth::id(),
        ];

        $SalesChart = new LaravelChart($SalesChart_options);

        $adminSales= Order::with(['buyer', 'seller', 'items.product.category' , 'items.product', 'items.seller'])->orderBy('created_at', 'desc')->paginate(10);

        return view('salesHistory.index', compact('sales', 'SalesChart', 'adminSales'));
    }

    public function pdf(Request $request)
    {
        $query = Order::whereHas('items.product', function ($subQuery) {
            $subQuery->where('seller_id', Auth::id());
        })->with([
            'buyer',
            'seller',
            'items' => function ($q) {
                $q->whereHas('product', function ($subQuery) {
                    $subQuery->where('seller_id', Auth::id());
                })->with('product.category', 'product');
            }
        ]);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $sales = $query->orderBy('created_at', 'desc')->get();

        $adminSales= Order::with(['buyer', 'seller', 'items.product.category' , 'items.product', 'items.seller'])->orderBy('created_at', 'desc')->get();
        $pdf = Pdf::loadView('salesHistory.pdf', compact('sales', 'adminSales'));
        return $pdf->stream('salesHistory.pdf');
    }
}
