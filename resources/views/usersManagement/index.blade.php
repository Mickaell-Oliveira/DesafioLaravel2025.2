@extends('adminlte::page')

@section('title', 'Gerenciar Usuários')

@section('content_header')
    <h1>Gerenciar Usuários</h1>
    @auth
        @if (auth()->user()->type === 'admin')
            <button class="btn btn-success" data-toggle="modal" data-target="#modal-create">Novo Usuário</button>
        @endif
    @endauth
@stop

@section('content')
    <div class="card mt-3">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->type }}</td>
                            <td>
                                <!-- Botão Visualizar -->
                                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal-view-{{ $user->id }}">Visualizar</button>

                                @auth
                                    @if (auth()->user()->type === 'admin')
                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modal-edit-{{ $user->id }}">Editar</button>
                                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal-delete-{{ $user->id }}">Excluir</button>
                                    @endif
                                @endauth
                            </td>
                        </tr>

                        <!-- Modal Visualizar Usuário -->
                        <div class="modal fade" id="modal-view-{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Visualizar Usuário</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>ID:</strong> {{ $user->id }}</p>
                                        <p><strong>Nome:</strong> {{ $user->name }}</p>
                                        <p><strong>Email:</strong> {{ $user->email }}</p>
                                        <p><strong>Telefone:</strong> {{ $user->phone }}</p>
                                        <p><strong>Data de Nascimento:</strong> {{ $user->birth_date }}</p>
                                        <p><strong>CPF:</strong> {{ $user->cpf }}</p>
                                        <p><strong>Saldo:</strong> R$ {{ number_format($user->saldo, 2, ',', '.') }}</p>
                                        <p><strong>Tipo:</strong> {{ $user->type }}</p>
                                        <p><strong>Criado em:</strong> {{ $user->created_at }}</p>
                                        <p><strong>Atualizado em:</strong> {{ $user->updated_at }}</p>
                                        @if($user->photo)
                                            <p><strong>Foto:</strong></p>
                                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto do usuário" width="150">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Editar Usuário -->
                        <div class="modal fade" id="modal-edit-{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('usersManagement.update', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Editar Usuário</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">

                                            <div class="form-group">
                                                <label>Nome</label>
                                                <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Telefone</label>
                                                <input type="text" class="form-control" name="phone" value="{{ $user->phone }}">
                                            </div>

                                            <div class="form-group">
                                                <label>Data de Nascimento</label>
                                                <input type="date" class="form-control" name="birth_date" value="{{ $user->birth_date }}">
                                            </div>

                                            <div class="form-group">
                                                <label>CPF</label>
                                                <input type="text" class="form-control" name="cpf" value="{{ $user->cpf }}">
                                            </div>

                                            <div class="form-group">
                                                <label>Saldo</label>
                                                <input type="number" step="0.01" class="form-control" name="saldo" value="{{ $user->saldo }}">
                                            </div>

                                            <div class="form-group">
                                                <label>Tipo</label>
                                                <select class="form-control" name="type">
                                                    <option value="admin" @if($user->type == 'admin') selected @endif>Admin</option>
                                                    <option value="user" @if($user->type == 'user') selected @endif>Usuário</option>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Salvar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Excluir Usuário -->
                        <div class="modal fade" id="modal-delete-{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('usersManagement.destroy', $user->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Excluir Usuário</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>

                                        <div class="modal-body">
                                            <p>Tem certeza que deseja excluir o usuário <strong>{{ $user->name }}</strong>?</p>
                                        </div>
                                        
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-danger">Excluir</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Nenhum usuário encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Modal Criar Usuário -->
    <div class="modal fade" id="modal-create" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('usersManagement.store') }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Novo Usuário</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group"><label>Nome</label><input type="text" class="form-control" name="name" required></div>
                        <div class="form-group"><label>Email</label><input type="email" class="form-control" name="email" required></div>
                        <div class="form-group"><label>Senha</label><input type="password" class="form-control" name="password" required autocomplete="new-password"></div>
                        <div class="form-group"><label>Telefone</label><input type="text" class="form-control" name="phone"></div>
                        <div class="form-group"><label>Data de Nascimento</label><input type="date" class="form-control" name="birth_date"></div>
                        <div class="form-group"><label>CPF</label><input type="text" class="form-control" name="cpf"></div>
                        <div class="form-group"><label>Saldo</label><input type="number" step="0.01" class="form-control" name="saldo"></div>
                        <div class="form-group"><label>Foto (opcional)</label><input type="file" class="form-control" name="photo" accept="image/*"></div>
                        <div class="form-group">
                            <label>Tipo</label>
                            <select class="form-control" name="type">
                                <option value="admin">Admin</option>
                                <option value="user">Usuário</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Criar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
