<?php

use App\Models\Product;

function formatPrice($price) {
    return 'R$ ' . number_format($price, 1, ',', '.');
}

function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function cartTotal($cart) {
    return array_reduce($cart, function($total, $item) {
        return $total + ($item['price'] * $item['quantity']);
    }, 0);
}

function verifyStock($productId, $quantity) {
    if(Product::find($productId)->quantity >= $quantity ) {
        return true;
    }
    return false;
}
