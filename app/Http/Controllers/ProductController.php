<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Storage;
use LaravelDaily\LaravelCharts\Classes\LaravelChart;
use Illuminate\Support\Facades\Auth;


class ProductController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Product::where('name', 'like', "%{$query}%")->get();
        return view('initialPage.index', compact('results'));
    }

    // Página principal de produtos
    public function index(Request $request)
    {
        $query = $request->input('query');
        $categoryId = $request->input('category');
        $products = Product::when($query, function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%");
        })
        ->when($categoryId, function($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        })->orderby('created_at', 'desc')->paginate(10);
        $categories = Category::all();
        return view('initialPage.index', [
            'products' => $products,
            'query' => $query,
            'categories' => $categories,
        ]);
    }

    // Página de gerenciamento de produtos
    public function management(Request $request)
    {
        $query = $request->input('query');
        $products = Product::with('category', 'seller')
            ->when($query, function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $categories = Category::all();

        // Criação do gráfico
        $chart_options = [
            'chart_title'           => 'Produtos Cadastrados por Mês',
            'model'                 =>  Product::class,
            'chart_type'            => 'bar',
            'report_type'           => 'group_by_date',
            'group_by_field'        => 'created_at',
            'group_by_period'       => 'month',
            'chart_color'           => '0,122,255',
            'filter_field'          => 'created_at',
            'filter_days'           => 365,
        ];
        $chart = new LaravelChart($chart_options);

        return view('productsManagement.index', compact('products', 'query', 'categories', 'chart'));
    }

    public function create()
    {
        return redirect()->route('productsManagement.index');
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('productPage.index', compact('product'));
    }

    public function getCategories()
    {
        $categories = Category::all();
        return $categories;
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->all();

        if($request->hasFile('photo'))
        {
            if($product->photo && Storage::disk('public')->exists($product->photo)) {
                Storage::disk('public')->delete($product->photo);
            }

            $path = $request->file('photo')->store('products', 'public');
            $data['photo'] = $path;
        }


        $product->update($data);
        return redirect()->route('productsManagement.index')->with('success', 'Produto atualizado com sucesso!');
    }

public function destroy($id)
{
    $product = Product::findOrFail($id);
    if ($product->photo && Storage::disk('public')->exists($product->photo)) {
        Storage::disk('public')->delete($product->photo);
    }
    $product->delete();

    return redirect()->route('productsManagement.index')->with('success', 'Produto excluído com sucesso!');
}

    public function store(Request $request)
    {
        $data = $request->all();

        if($request->hasFile('photo'))
        {
            $path = $request->file('photo')->store('products', 'public');
            $data['photo'] = $path;
        }
        Product::create($data);
        return redirect()->route('productsManagement.index');
    }

}
