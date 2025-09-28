<?php

use App\Models\Product;

function formatPrice($price) { // formata o preço com duas casas decimais e vírgula
    return 'R$ ' . number_format($price, 2, ',', '.');
}

function formatDate($date) { // formata a data para o formato dd/mm/yyyy
    return date('d/m/Y', strtotime($date));
}

function cartTotal($cart) { // calcula o total do carrinho
    return array_reduce($cart, function($total, $item) {
        return $total + ($item['price'] * $item['quantity']);
    }, 0);
}

function verifyStock($productId, $quantity) { // verifica se há estoque suficiente para o produto
    if(Product::find($productId)->quantity >= $quantity ) {
        return true;
    }
    return false;
}
