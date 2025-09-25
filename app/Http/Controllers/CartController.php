<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = session()->get('cart', []);
        $total = 0;

        // Calcula o total
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = $request->input('quantity');

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $product->price,
                "photo" => $product->photo
            ];
        }

        $product->quantity -= $quantity;
        $product->save();
        
        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Produto adicionado ao carrinho!');
    }

    public function update(Request $request, $productId)
    {
        $cart = session()->get('cart');

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Carrinho atualizado com sucesso!');
        }

        return redirect()->route('cart.index')->with('error', 'Item não encontrado no carrinho.');
    }

    public function remove($productId)
    {
        $cart = session()->get('cart');

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Produto removido do carrinho!');
        }

        return redirect()->route('cart.index')->with('error', 'Item não encontrado no carrinho.');
    }

    public function checkout(Request $request)
    {
        $url = config('services.pagseguro.checkout_url');
        $token = config('services.pagseguro.token');

        $cartItems = json_decode($request->cart_items, true);

        $items = array_values(array_map(fn($item)=>[
            'name' => $item['name'],
            'quantity' => $item['quantity'],
            'unit_amount' => $item['price'] * 100
        ], $cartItems));

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-type' => 'application/json'
        ])->withoutVerifying()->post($url,[
            'reference_id' => uniqid(),
            'items' => $items,
        ]);

        if($response->successful())
        {
            $total = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);

            $order = Order::create([
                'id' => $response['reference_id'],
                'status' => 1,
                'buyer_id' => Auth::id(),
                'total' => $total
            ]);

            Transaction::create([
                'user_id' => Auth::id(),
                'order_id' => $order->id,
                'amount' => $total
            ]);

        foreach ($cartItems as $productId => $item) {
            $product = Product::find($productId);
            $seller = User::find($product->user_id);

            $paymentAmount = $item['price'] * $item['quantity'];

            $seller->saldo += $paymentAmount;
            $seller->save();

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'seller_id' => $product->user_id,
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

            $pay_link = data_get($response->json(), 'links.1.href');

            session()->forget('cart');
            return redirect()->away($pay_link);
        }

        return redirect('paymentError');
    }

    public function paymentError()
    {
        return view('cart.paymentError');
    }
}
