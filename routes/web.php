<?php

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
Route::prefix('users')->middleware(['auth', 'admin'])->name('usersManagement.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::patch('/{user}', [UserController::class, 'adminUpdate'])->name('update');
    Route::delete('/{user}', [UserController::class, 'adminDestroy'])->name('destroy');
});

// Rotas para administradores gerenciarem administradores
Route::prefix('admins')->middleware(['auth', 'admin'])->name('adminManagement.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::post('/', [AdminController::class, 'adminStore'])->name('store');
    Route::patch('/{admin}', [AdminController::class, 'adminUpdate'])->name('update');
    Route::delete('/{admin}', [AdminController::class, 'adminDestroy'])->name('destroy');
});

// Página inicial
Route::get('/initialPage', [InitialPageController::class, 'index'])->name('initialPage');

// Página de pesquisa de produtos
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');

// Página de gerenciamento de produtos
Route::get('/admin',[AdminController::class, 'index'])->middleware(['auth', 'admin'])->name('admin.index');

// Página de gerenciamento de administradores
Route::get('/adminManagement',[AdminController::class, 'index'])->middleware(['auth', 'admin'])->name('adminManagement.index');

// Página de gerenciamento de usuários
Route::get('/usersManagement',[UserController::class, 'index'])->middleware(['auth', 'admin'])->name('usersManagement.index');

// Rota para enviar email para usuário
Route::post('/usersManagement/{user}/send-email', [UserController::class, 'sendEmail'])->middleware(['auth', 'admin'])->name('usersManagement.sendEmail');

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
