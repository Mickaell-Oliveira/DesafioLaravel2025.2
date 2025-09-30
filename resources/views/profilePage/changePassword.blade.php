@extends('adminlte::page')

@section('title', 'Alterar Senha')

@section('content_header')
    <h1>Alterar Senha</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="post" action="{{ route('password.update') }}">
                @csrf
                @method('put')
                <div class="form-group">
                    <label for="currentPassword">Senha Atual</label>
                    <input id="currentPassword" name="currentPassword" type="password" class="form-control">
                    @error('currentPassword', 'updatePassword')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Nova Senha</label>
                    <input id="password" name="password" type="password" class="form-control" autocomplete="new-password">
                    @error('password', 'updatePassword')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="passwordConfirmation">Confirmar Nova Senha</label>
                    <input id="passwordConfirmation" name="passwordConfirmation" type="password" class="form-control" autocomplete="new-password">
                    @error('passwordConfirmation', 'updatePassword')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="d-flex align-items-center">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
@stop
