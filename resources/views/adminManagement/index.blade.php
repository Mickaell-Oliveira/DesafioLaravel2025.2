@vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/cepAPI.js'])
@extends('adminlte::page')

@section('title', 'Gerenciar Administradores')

@section('content_header')
    <h1>Gerenciar Administradores</h1>
            <button class="btn btn-success" data-toggle="modal" data-target="#modal-create">Novo Administrador</button>

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
                    @forelse($admins as $admin)
                        <tr>
                            <td>{{ $admin->id }}</td>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->type }}</td>
                            <td>
                                <!-- Botão Visualizar -->
                                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal-view-{{ $admin->id }}">Visualizar</button>
                                @auth
                                    @if (auth()->user()->id === $admin->created_by)
                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modal-edit-{{ $admin->id }}">Editar</button>
                                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal-delete-{{ $admin->id }}">Excluir</button>
                                    @endif
                                @endauth
                            </td>
                        </tr>

                        <!-- Modal Visualizar Administrador -->
                        <div class="modal fade" id="modal-view-{{ $admin->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Visualizar Administrador</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>ID:</strong> {{ $admin->id }}</p>
                                        <p><strong>Nome:</strong> {{ $admin->name }}</p>
                                        <p><strong>Email:</strong> {{ $admin->email }}</p>
                                        <p><strong>Telefone:</strong> {{ $admin->phone }}</p>
                                        <p><strong>Data de Nascimento:</strong> {{ $admin->birth_date }}</p>
                                        <p><strong>CPF:</strong> {{ $admin->cpf }}</p>
                                        <p><strong>Saldo:</strong> R$ {{ number_format($admin->saldo, 2, ',', '.') }}</p>
                                        <p><strong>Tipo:</strong> {{ $admin->type }}</p>
                                        <p><strong>Criado em:</strong> {{ $admin->created_at }}</p>
                                        <p><strong>Atualizado em:</strong> {{ $admin->updated_at }}</p>
                                        @if($admin->photo)
                                            <p><strong>Foto:</strong></p>
                                            <img src="{{ asset('storage/' . $admin->photo) }}" alt="Foto do administrador" width="150">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Editar Administrador -->
                        <div class="modal fade modal-endereco" id="modal-edit-{{ $admin->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('adminManagement.update', $admin->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Editar Administrador</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">

                                            <div class="form-group">
                                                <label>Nome</label>
                                                <input type="text" class="form-control" name="name" value="{{ $admin->name }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" class="form-control" name="email" value="{{ $admin->email }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Telefone</label>
                                                <input type="text" class="form-control" name="phone" value="{{ $admin->phone }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Data de Nascimento</label>
                                                <input type="date" class="form-control" name="birth_date" value="{{ $admin->birth_date }}">
                                            </div>
                                            <div class="form-group">
                                                <label>CPF</label>
                                                <input type="text" class="form-control" name="cpf" value="{{ $admin->cpf }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Saldo</label>
                                                <input type="number" step="0.01" class="form-control" name="saldo" value="{{ $admin->saldo }}">
                                            </div>
                                            <hr>
                                            <h5>Endereço</h5>
                                            <div class="form-group">
                                                <label>CEP</label>
                                                <input type="text" class="form-control cep-input" name="cep" value="{{ $admin->address->cep ?? '' }}" id="cep-edit-{{ $admin->id }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Número</label>
                                                <input type="text" class="form-control" name="numero" value="{{ $admin->address->numero ?? '' }}" id="numero-edit-{{ $admin->id }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Logradouro</label>
                                                <input type="text" class="form-control logradouro-input" name="logradouro" value="{{ $admin->address->logradouro ?? '' }}" id="logradouro-edit-{{ $admin->id }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Bairro</label>
                                                <input type="text" class="form-control bairro-input" name="bairro" value="{{ $admin->address->bairro ?? '' }}" id="bairro-edit-{{ $admin->id }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Cidade</label>
                                                <input type="text" class="form-control cidade-input" name="cidade" value="{{ $admin->address->cidade ?? '' }}" id="cidade-edit-{{ $admin->id }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Estado</label>
                                                <input type="text" class="form-control estado-input" name="estado" value="{{ $admin->address->estado ?? '' }}" id="estado-edit-{{ $admin->id }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Complemento</label>
                                                <input type="text" class="form-control complemento-input" name="complemento" value="{{ $admin->address->complemento ?? '' }}" id="complemento-edit-{{ $admin->id }}">
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
                        <div class="modal fade" id="modal-delete-{{ $admin->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('adminManagement.destroy', $admin->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Excluir Administrador</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>

                                        <div class="modal-body">
                                            <p>Tem certeza que deseja excluir o administrador <strong>{{ $admin->name }}</strong>?</p>
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
                {{ $admins->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Modal Criar Usuário -->
    <div class="modal fade modal-endereco" id="modal-create" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('adminManagement.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Novo Administrador</h5>
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
                        <hr>
                        <h5>Endereço</h5>
                        <div class="form-group"><label>CEP</label><input type="text" class="form-control cep-input" name="cep" id="cep-create"></div>
                        <div class="form-group"><label>Número</label><input type="text" class="form-control" name="numero" id="numero-create"></div>
                        <div class="form-group"><label>Logradouro</label><input type="text" class="form-control logradouro-input" name="logradouro" id="logradouro-create"></div>
                        <div class="form-group"><label>Bairro</label><input type="text" class="form-control bairro-input" name="bairro" id="bairro-create"></div>
                        <div class="form-group"><label>Cidade</label><input type="text" class="form-control cidade-input" name="cidade" id="cidade-create"></div>
                        <div class="form-group"><label>Estado</label><input type="text" class="form-control estado-input" name="estado" id="estado-create"></div>
                        <div class="form-group"><label>Complemento</label><input type="text" class="form-control complemento-input" name="complemento" id="complemento-create"></div>
                        <div class="form-group"><label>Foto (opcional)</label><input type="file" class="form-control" name="photo" accept="image/*"></div>
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

