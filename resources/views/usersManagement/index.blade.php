@vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/cepAPI.js'])

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
                                        <button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#modal-email-{{ $user->id }}">Enviar Email</button>
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
                                        <hr>
                                        <h5>Endereço</h5>
                                        <p><strong>Cep:</strong> {{ $user->address->cep }}</p>
                                        <p><strong>Número:</strong> {{ $user->address->numero }}</p>
                                        <p><strong>Logradouro:</strong> {{ $user->address->logradouro }}</p>
                                        <p><strong>Bairro:</strong> {{ $user->address->bairro }}</p>
                                        <p><strong>Cidade:</strong> {{ $user->address->cidade }}</p>
                                        <p><strong>Estado:</strong> {{ $user->address->estado }}</p>
                                        <p><strong>Complemento:</strong> {{ $user->address->complemento }}</p>
                                        <hr>
                                        <p><strong>Criado em:</strong> {{ $user->created_at }}</p>
                                        <p><strong>Atualizado em:</strong> {{ $user->updated_at }}</p>
                                        @if($user->photo)
                                            <p><strong>Foto:</strong></p>
                                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto do usuário" class="img-fluid rounded mb-3" width="150">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Editar Usuário -->
                        <div class="modal fade" id="modal-edit-{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('usersManagement.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Editar Usuário</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">

                                            <div class="form-group">
                                                <label for="name-{{ $user->id }}">Nome</label>
                                                <input type="text" class="form-control" id="name-{{ $user->id }}" name="name" value="{{ $user->name }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="email-{{ $user->id }}">Email</label>
                                                <input type="email" class="form-control" id="email-{{ $user->id }}" name="email" value="{{ $user->email }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="phone-{{ $user->id }}">Telefone</label>
                                                <input type="text" class="form-control" id="phone-{{ $user->id }}" name="phone" value="{{ $user->phone }}">
                                            </div>

                                            <div class="form-group">
                                                <label for="cpf-{{ $user->id }}">CPF</label>
                                                <input type="text" class="form-control" id="cpf-{{ $user->id }}" name="cpf" value="{{ $user->cpf }}">
                                            </div>

                                            <div class="form-group">
                                                <label for="birth_date-{{ $user->id }}">Data de Nascimento</label>
                                                <input type="date" class="form-control" id="birth_date-{{ $user->id }}" name="birth_date" value="{{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('Y-m-d') : '' }}">
                                            </div>

                                            <div class="form-group">
                                                <label for="saldo-{{ $user->id }}">Saldo</label>
                                                <input type="number" class="form-control" id="saldo-{{ $user->id }}" name="saldo" value="{{ $user->saldo }}">
                                            </div>
                                            <hr>
                                            <h5>Endereço</h5>
                                            <div class="form-group">
                                                <label for="cep-{{ $user->id }}">CEP</label>
                                                <input type="text" class="form-control cep-input" id="cep-{{ $user->id }}" name="cep" value="{{ $user->address->cep }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="numero-{{ $user->id }}">Número</label>
                                                <input type="text" class="form-control" id="numero-{{ $user->id }}" name="numero" value="{{ $user->address->numero }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="logradouro-{{ $user->id }}">Logradouro</label>
                                                <input type="text" class="form-control logradouro-input" id="logradouro-{{ $user->id }}" name="logradouro" value="{{ $user->address->logradouro }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="bairro-{{ $user->id }}">Bairro</label>
                                                <input type="text" class="form-control bairro-input" id="bairro-{{ $user->id }}" name="bairro" value="{{ $user->address->bairro }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="cidade-{{ $user->id }}">Cidade</label>
                                                <input type="text" class="form-control cidade-input" id="cidade-{{ $user->id }}" name="cidade" value="{{ $user->address->cidade }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="estado-{{ $user->id }}">Estado</label>
                                                <input type="text" class="form-control estado-input" id="estado-{{ $user->id }}" name="estado" value="{{ $user->address->estado }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="complemento-{{ $user->id }}">Complemento</label>
                                                <input type="text" class="form-control complemento-input" id="complemento-{{ $user->id }}" name="complemento" value="{{ $user->address->complemento }}">
                                            </div>

                                            <!-- Upload de Foto -->
                                            <div class="form-group">
                                                <label for="photo-{{ $user->id }}">Foto</label>
                                                <div class="mt-2 mb-2 text-center">
                                                    <img id="preview-photo-{{ $user->id }}"
                                                         src="{{ $user->photo ? asset('storage/' . $user->photo) : 'https://via.placeholder.com/150' }}"
                                                         alt="Foto do usuário"
                                                         class="img-fluid rounded mb-2" width="150">

                                                    <input type="file" id="photo-{{ $user->id }}" name="photo" class="d-none" accept="image/*"
                                                           onchange="previewUserImage(event, {{ $user->id }})">
                                                    <label for="photo-{{ $user->id }}" class="btn btn-primary btn-sm mt-2">Escolher nova foto</label>
                                                </div>
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

                        <!-- Modal Enviar Email -->
                        <div class="modal fade" id="modal-email-{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('usersManagement.sendEmail', $user->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Enviar Email para {{ $user->name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="subject">Assunto</label>
                                                <input type="text" class="form-control" name="subject" id="subject" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="message">Mensagem</label>
                                                <textarea class="form-control" name="message" id="message" rows="4" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Enviar</button>
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
                        <hr>
                        <h5>Endereço</h5>
                        <div class="form-group"><label>CEP</label><input type="text" class="form-control cep-input" name="cep" id="cep-create"></div>
                        <div class="form-group"><label>Número</label><input type="text" class="form-control" name="numero" id="numero-create"></div>
                        <div class="form-group"><label>Logradouro</label><input type="text" class="form-control logradouro-input" name="logradouro" id="logradouro-create"></div>
                        <div class="form-group"><label>Bairro</label><input type="text" class="form-control bairro-input" name="bairro" id="bairro-create"></div>
                        <div class="form-group"><label>Cidade</label><input type="text" class="form-control cidade-input" name="cidade" id="cidade-create"></div>
                        <div class="form-group"><label>Estado</label><input type="text" class="form-control estado-input" name="estado" id="estado-create"></div>
                        <div class="form-group"><label>Complemento</label><input type="text" class="form-control complemento-input" name="complemento" id="complemento-create"></div>
                        <div class="form-group">
                            <label for="photo-create">Foto (opcional)</label>
                            <div class="mt-2 mb-2 text-center">
                                <img id="preview-photo-create" src="https://via.placeholder.com/150"
                                     alt="Preview da foto" class="img-fluid rounded mb-2" width="150">
                                <input type="file" id="photo-create" name="photo" class="d-none" accept="image/*"
                                       onchange="previewUserImage(event, 'create')">
                                <label for="photo-create" class="btn btn-primary btn-sm mt-2">Escolher foto</label>
                            </div>
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
@section('js')
<script>
    function previewUserImage(event, id) {
        const input = event.target;
        const preview = document.getElementById('preview-photo-' + id);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
