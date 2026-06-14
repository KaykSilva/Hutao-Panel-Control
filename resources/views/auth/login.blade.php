@extends('layouts.app', ['title' => 'Entrar'])

@section('content')
<section class="auth-card">
    <h1>Entrar</h1>
    <p class="muted">Acesse o painel da plataforma.</p>

    @if ($errors->any())
        <div class="errors">{{ $errors->first() }}</div>
    @endif

    <form method="post" action="{{ route('login') }}" class="form">
        @csrf
        <label>Email
            <input name="email" type="email" value="{{ old('email') }}" required autofocus>
        </label>
        <label>Senha
            <input name="password" type="password" required>
        </label>
        <label class="row">
            <input name="remember" type="checkbox" value="1" style="width:auto"> Manter conectado
        </label>
        <button class="button" type="submit">Entrar</button>
        <a class="button secondary" href="{{ route('register') }}">Criar conta</a>
    </form>
</section>
@endsection
