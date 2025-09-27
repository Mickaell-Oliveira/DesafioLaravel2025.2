@extends('adminlte::page')

@section('title', 'Gerenciar Produtos')

@section('content_header')
    <h1>Gerenciar Produtos</h1>
    @auth
        @if (auth()->user()->type === 'user')
            <button class="btn btn-success" data-toggle="modal" data-target="#modal-create">Novo Produto</button>
        @endif
    @endauth
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop

@section('content')
    <div class="card mt-3">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td data-label="ID">{{ $product->id }}</td>
                            <td data-label="Nome">{{ $product->name }}</td>
                            <td data-label="Categoria">{{ $product->category->name ?? '-' }}</td>
                            <td data-label="Preço">R$ {{ formatPrice($product->price) }}</td>
                            <td data-label="Quantidade">{{ $product->quantity }}</td>
                            <td data-label="Ações">
                                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal-{{ $product->id }}">Visualizar</button>
                                @auth
                                    @if (auth()->user()->type === 'admin' ||  auth()->user()->id === $product->user_id)
                                        <!-- Botão Editar -->
                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modal-edit-{{ $product->id }}">Editar</button>
                                    <!-- Modal Deletar -->
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</button>
                                        </form>
                                    @endif
                                @endauth


                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Nenhum produto cadastrado.</td>
                        </tr>
                    @endforelse

        <!-- Modais -->
        @foreach($products as $product)
            <!-- Modal Visualizar -->
            <div class="modal fade" id="modal-{{ $product->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $product->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel{{ $product->id }}">{{ $product->name }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <img src="{{ asset('storage/' . $product->photo) }}" alt="Product Image" class="img-fluid mb-3">
                            <p><strong>Categoria:</strong> {{ $product->category->name }}</p>
                            <p><strong>Preço:</strong> R$ {{ formatPrice($product->price) }}</p>
                            <p><strong>Quantidade:</strong> {{ $product->quantity }}</p>
                            <p><strong>Descrição:</strong> {{ $product->description }}</p>
                            <p><strong>Criado por:</strong> {{ $product->seller->name }}</p>
                            <p><strong>Última Atualização:</strong> {{ $product->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de edição -->
            <div class="modal fade" id="modal-edit-{{ $product->id }}" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel{{ $product->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title text-dark" id="modalEditLabel{{ $product->id }}">Editar Produto</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="name-{{ $product->id }}">Nome</label>
                                    <input type="text" class="form-control" id="name-{{ $product->id }}" name="name" value="{{ $product->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="photo-{{ $product->id }}">Foto</label>
                                    <div class="mt-2 mb-2 text-center">
                                        <img id="preview-photo-{{ $product->id }}" src="{{ asset('storage/' . $product->photo) }}" alt="Product Image" class="img-fluid mb-2">
                                        <input type="file" id="photo-{{ $product->id }}" name="photo" class="d-none" accept="image/*"
                                            onchange="previewImage(event, {{ $product->id }})">
                                        <label for="photo-{{ $product->id }}" class="btn btn-primary btn-sm mt-2">Escolher nova foto</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="category-{{ $product->id }}">Categoria</label>
                                    <select class="form-control" id="category-{{ $product->id }}" name="category_id" required>
                                        <option value="">Selecione uma categoria</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="price-{{ $product->id }}">Preço</label>
                                    <input type="number" step="0.01" class="form-control" id="price-{{ $product->id }}" name="price" value="{{ $product->price }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="quantity-{{ $product->id }}">Quantidade</label>
                                    <input type="number" class="form-control" id="quantity-{{ $product->id }}" name="quantity" value="{{ $product->quantity }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="description-{{ $product->id }}">Descrição</label>
                                    <textarea class="form-control" id="description-{{ $product->id }}" name="description" required>{{ $product->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="seller-{{ $product->id }}">Vendedor</label>
                                    <a>{{ $product->seller->name }}</a>
                                </div>
                                <div class="form-group">
                                    <label for="updated_at-{{ $product->id }}">Última Atualização</label>
                                    <a>{{ $product->updated_at->format('d/m/Y H:i') }}</a>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-warning text-dark">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Modal de criar -->
        <div class="modal fade" id="modal-create" tabindex="-1" role="dialog" aria-labelledby="modalCreateLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title text-white" id="modalCreateLabel">Novo Produto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <!--Inserir Foto-->
                            <div>
                                <label for="photo-create">Foto</label>
                                <input type="file" class="form-control" id="photo-create" name="photo" required>
                            </div>
                            <div class="form-group">
                                <label for="name-create">Nome</label>
                                <input type="text" class="form-control" id="name-create" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="category-create">Categoria</label>
                                <select class="form-control" id="category-create" name="category_id" required>
                                    <option value="">Selecione uma categoria</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="price-create">Preço</label>
                                <input type="number" step="0.01" class="form-control" id="price-create" name="price" required>
                            </div>
                            <div class="form-group">
                                <label for="quantity-create">Quantidade</label>
                                <input type="number" class="form-control" id="quantity-create" name="quantity" required>
                            </div>
                            <div class="form-group">
                                <label for="description-create">Descrição</label>
                                <textarea class="form-control" id="description-create" name="description" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">Criar</button>
                        </div>
                    @auth
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    @endauth
                    </form>
                </div>
            </div>
        </div>
                </tbody>
            </table>
            <div class="mt-3">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    {{-- Gráfico de produtos cadastrados por mês --}}
    @auth
        @if(auth()->user()->type === 'admin')
            <div class="card mt-4">
                <div class="card-body">
                    <h4 class="mb-3">Produtos cadastrados por mês</h4>
                    {!! $chart->renderHtml() !!}
                </div>
            </div>
            {!! $chart->renderChartJsLibrary() !!}
            {!! $chart->renderJs() !!}
        @endif
    @endauth
@stop
