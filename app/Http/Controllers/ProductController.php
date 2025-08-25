<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

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
        $products = Product::when($query, function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%");
        })->paginate(10);
        return view('initialPage.index', ['products' => $products, 'query' => $query]);
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
        return view('productsManagement.index', compact('products', 'query', 'categories'));
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
        $product->update($request->all());
        return redirect()->route('productsManagement.index');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('productsManagement.index');
    }

    public function store(Request $request)
    {
        Product::create($request->all());
        return redirect()->route('productsManagement.index');
    }

}
