@vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/cepAPI.js'])

@extends('adminlte::page')

@section('title', 'Meu Perfil')

@section('content_header')
    <h1>Meu Perfil</h1>
@stop


@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="icon" href="/vendor/adminlte/dist/img/userIcon.jpeg" type="image/jpeg">
@stop

@section('content')
    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Informações do Usuário</h3>
            <div>
                <button id="editProfileBtn" type="button" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Editar Perfil
                </button>

                <form method="POST" action="{{ route('profilePage.destroy') }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.')">
                        <i class="fas fa-trash"></i> Excluir Conta
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body">
            <form id="profileForm" method="POST" action="{{ route('profilePage.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name">Nome</label>
                        <input type="text" name="name" id="name"
                               value="{{ old('name', $user->name) }}"
                               class="form-control" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', $user->email) }}"
                               class="form-control" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone">Telefone</label>
                        <input type="text" name="phone" id="phone"
                               value="{{ old('phone', $user->phone) }}"
                               class="form-control" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="birth_date">Data de Nascimento</label>
                        <input type="date" name="birth_date" id="birth_date"
                               value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}"
                               class="form-control" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="cpf">CPF</label>
                        <input type="text" name="cpf" id="cpf"
                               value="{{ old('cpf', $user->cpf) }}"
                               class="form-control" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="photo-{{ $user->id }}">Foto</label>
                        <div class="d-flex flex-column align-items-center">
                            <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('vendor/adminlte/dist/img/person.png') }}" alt="sem foto" id="preview-photo-{{ $user->id }}" class="img-thumbnail mb-2" style="width:180px; height:180px; object-fit:cover; border-radius:50%; background:#eee;">
                            <input type="file" name="photo" id="photo-{{ $user->id }}" class="form-control d-none" accept="image/*" onchange="previewUserImage(event, {{ $user->id }})">
                            <button type="button" id="changePhotoBtn-{{ $user->id }}" class="btn btn-secondary btn-sm mt-2" style="display:none;" onclick="document.getElementById('photo-{{ $user->id }}').click()">
                                Mudar foto
                            </button>
                        </div>
                    </div>
                </div>

                <hr>
                <h4>Endereço</h4>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="cep">CEP</label>
                        <input type="text" name="cep" id="cep"
                               value="{{ $user->address->cep ?? '' }}"
                               class="form-control cep-input" disabled>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="numero">Número</label>
                        <input type="text" name="numero" id="numero"
                               value="{{ $user->address->numero ?? '' }}"
                               class="form-control" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="logradouro">Logradouro</label>
                        <input type="text" name="logradouro" id="logradouro"
                               value="{{ $user->address->logradouro ?? '' }}"
                               class="form-control logradouro-input" disabled>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="bairro">Bairro</label>
                        <input type="text" name="bairro" id="bairro"
                               value="{{ $user->address->bairro ?? '' }}"
                               class="form-control bairro-input" disabled>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="cidade">Cidade</label>
                        <input type="text" name="cidade" id="cidade"
                               value="{{ $user->address->cidade ?? '' }}"
                               class="form-control cidade-input" disabled>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="estado">Estado</label>
                        <input type="text" name="estado" id="estado"
                               value="{{ $user->address->estado ?? '' }}"
                               class="form-control estado-input" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="complemento">Complemento</label>
                        <input type="text" name="complemento" id="complemento"
                               value="{{ $user->address->complemento ?? '' }}"
                               class="form-control" disabled>
                    </div>
                </div>

                <div class="mt-3 text-right">
                    <button id="saveBtn" type="submit" class="btn btn-success" style="display:none;">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    // Ao clicar no botão editar perfil habilita os campos para edição
    document.getElementById('editProfileBtn').addEventListener('click', function() {
        const inputs = document.querySelectorAll('#profileForm input');
        inputs.forEach(input => input.removeAttribute('disabled'));

        document.getElementById('saveBtn').style.display = 'inline-block';
        document.getElementById('changePhotoBtn-{{ $user->id }}').style.display = 'inline-block';

        const preview = document.getElementById('preview-photo-{{ $user->id }}');
        const fileInput = document.getElementById('photo-{{ $user->id }}');

        preview.style.cursor = 'pointer';
        preview.onclick = function() {
            fileInput.click();
        };
    });
</script>
@stop
