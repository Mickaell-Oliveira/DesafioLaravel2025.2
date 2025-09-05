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

// Rotas para o usuário autenticado (perfil próprio)
Route::prefix('profile')->middleware('auth')->name('profilePage.')->group(function () {
    Route::get('/', [UserController::class, 'profile'])->name('index');
    Route::patch('/', [UserController::class, 'update'])->name('update');
    Route::delete('/', [UserController::class, 'destroy'])->name('destroy');
});

// Rotas para administradores gerenciarem usuários
Route::prefix('users')->middleware(['auth', 'can:isAdmin'])->name('usersManagement.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::patch('/{user}', [UserController::class, 'adminUpdate'])->name('update');
    Route::delete('/{user}', [UserController::class, 'adminDestroy'])->name('destroy');
});
// Página inicial
Route::get('/initialPage', [InitialPageController::class, 'index'])->name('initialPage');

// Página de pesquisa de produtos
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');

// Página de gerenciamento de produtos
Route::get('/admin',[AdminController::class, 'index'])->middleware(['auth', 'admin'])->name('admin.index');

// Página de gerenciamento de usuários
Route::get('/usersManagement',[UserController::class, 'index'])->middleware(['auth'])->name('usersManagement.index');

// Página de perfil do usuário
//Route::get('/profile',[UserController::class, 'profile'])->middleware(['auth'])->name('profilePage.index');

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
