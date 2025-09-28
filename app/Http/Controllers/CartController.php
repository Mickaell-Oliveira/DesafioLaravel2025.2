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
    public function index() // exibe o carrinho
    {
        $cartItems = session()->get('cart', []); // Itens do carrinho
        $total = 0;

        // Calcula o total
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, Product $product) // adiciona um produto ao carrinho
    {
        $cart = session()->get('cart', []);

        $request->validate([ // valida a quantidade
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = $request->input('quantity'); // quantidade a ser adicionada

        if (isset($cart[$product->id])) { // se o produto já estiver no carrinho, atualiza a quantidade
            $cart[$product->id]['quantity'] += $quantity;
        } else { // se não, adiciona o produto ao carrinho
            $cart[$product->id] = [
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $product->price,
                "photo" => $product->photo
            ];
        }

        $product->quantity -= $quantity; // reduz a quantidade do produto no estoque
        $product->save(); // salva a alteração no banco de dados

        session()->put('cart', $cart); // atualiza a sessão do carrinho

        return redirect()->back()->with('success', 'Produto adicionado ao carrinho!');
    }

    public function update(Request $request, $productId) // atualiza a quantidade de um produto no carrinho
    {
        $cart = session()->get('cart');

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $request->quantity; // atualiza a quantidade
            session()->put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Carrinho atualizado com sucesso!');
        }

        return redirect()->route('cart.index')->with('error', 'Item não encontrado no carrinho.');
    }

    public function remove($productId) // remove um produto do carrinho
    {
        $cart = session()->get('cart');

        if (isset($cart[$productId])) { // se o produto estiver no carrinho, remove
            unset($cart[$productId]); // remove o item do carrinho
            session()->put('cart', $cart); // atualiza a sessão do carrinho
            return redirect()->route('cart.index')->with('success', 'Produto removido do carrinho!');
        }

        return redirect()->route('cart.index')->with('error', 'Item não encontrado no carrinho.');
    }

    public function checkout(Request $request) // Integração da API do PagSeguro
    {
        $url = config('services.pagseguro.checkout_url'); // URL da API
        $token = config('services.pagseguro.token'); // Token da API

        $cartItems = json_decode($request->cart_items, true); // Itens do carrinho

        $items = array_values(array_map(fn($item)=>[ // formata os itens para a API
            'name' => $item['name'], // nome do produto
            'quantity' => $item['quantity'], // quantidade
            'unit_amount' => $item['price'] * 100 // preço em centavos
        ], $cartItems));

        $response = Http::withHeaders([ // faz a requisição para a API
            'Authorization' => 'Bearer ' . $token, // token de autorização
            'Content-type' => 'application/json' // tipo de conteúdo
        ])->withoutVerifying()->post($url,[ // desabilita a verificação SSL
            'reference_id' => uniqid(), // id único da transação
            'items' => $items, // itens do carrinho
        ]);

        if($response->successful()) // se a requisição for bem sucedida
        {
            $total = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']); // calcula o total

            $order = Order::create([ // cria o pedido
                'id' => $response['reference_id'],
                'status' => 1,
                'buyer_id' => Auth::id(),
                'total' => $total
            ]);

            Transaction::create([ // cria a transação
                'user_id' => Auth::id(),
                'order_id' => $order->id,
                'amount' => $total
            ]);

        foreach ($cartItems as $productId => $item) { // para cada item do carrinho
            $product = Product::find($productId); // busca o produto
            $seller = User::find($product->user_id); // busca o vendedor

            $paymentAmount = $item['price'] * $item['quantity']; // calcula o valor a ser pago ao vendedor

            $seller->saldo += $paymentAmount; // adiciona o valor ao saldo do vendedor
            $seller->save();    // salva a alteração no banco de dados

            OrderItem::create([ // cria o item do pedido
                'order_id' => $order->id,
                'product_id' => $productId,
                'seller_id' => $product->user_id,
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

            $pay_link = data_get($response->json(), 'links.1.href'); // link de pagamento

            session()->forget('cart'); // limpa o carrinho
            return redirect()->away($pay_link); // redireciona para o link de pagamento
        }

        return redirect('paymentError');
    }

    public function paymentError() // exibe a página de erro de pagamento
    {
        return view('cart.paymentError');
    }
}
