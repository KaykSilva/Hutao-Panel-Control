@extends('layouts.app', ['title' => 'Criar conta'])

@section('content')
<section class="auth-card">
    <h1>Criar conta</h1>
    <p class="muted">Cadastre seu acesso de usuario.</p>

    @if ($errors->any())
        <div class="errors">{{ $errors->first() }}</div>
    @endif

    <form method="post" action="{{ route('register') }}" class="form">
        @csrf
        <label>Nome
            <input name="name" value="{{ old('name') }}" required autofocus>
        </label>
        <label>Email
            <input name="email" type="email" value="{{ old('email') }}" required>
        </label>
        <label>Telefone
            <input name="phone" value="{{ old('phone') }}" placeholder="5585999999999">
        </label>
        <label>Senha
            <input name="password" type="password" required>
        </label>
        <label>Confirmar senha
            <input name="password_confirmation" type="password" required>
        </label>
        <button class="button" type="submit">Cadastrar</button>
        <a class="button secondary" href="{{ route('login') }}">Ja tenho conta</a>
    </form>
</section>
@endsection
