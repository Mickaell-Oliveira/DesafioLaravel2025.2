<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InitialPageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->middleware(['auth', 'verified'])->name('initialPage.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// Página inicial
Route::get('/initialPage', [InitialPageController::class, 'index'])->name('initialPage');

// Página de pesquisa de produtos
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');

// Página de gerenciamento de produtos
Route::get('/admin',[AdminController::class, 'index'])->middleware(['auth', 'admin'])->name('admin.index');

// Página de gerenciamento de usuários
Route::get('/user',[UserController::class, 'index'])->middleware(['auth', 'user'])->name('user.index');

// Página de produtos
Route::get('/products',[ProductController::class, 'index'])->name('products.index');

// Página de post individual
Route::get('/products/{id}',[ProductController::class, 'show'])->name('products.show');
// Página de gerenciamento de produtos
Route::get('/productsManagement', [ProductController::class, 'management'])->name('productsManagement.index');

// CRUD de Produtos
Route::get('/productsManagement/create',[ProductController::class, 'create'])->name('products.create');
Route::post('/productsManagement',[ProductController::class, 'store'])->name('products.store');
Route::get('/productsManagement/{id}/edit',[ProductController::class, 'edit'])->name('products.edit');
Route::put('/productsManagement/{id}',[ProductController::class, 'update'])->name('products.update');
Route::delete('/productsManagement/{id}',[ProductController::class, 'destroy'])->name('products.destroy');

//Rota para passar as categorias disponiveis para o modal editar
Route::get('/productsManagement/categories',[ProductController::class, 'getCategories'])->name('products.categories');

require __DIR__.'/auth.php';
