@extends('layouts.app', ['title' => 'Perfil'])

@section('content')
<div class="topbar">
    <div>
        <h1>Perfil</h1>
        <p class="muted">Dados usados pela plataforma e pelo bot.</p>
    </div>
</div>

@if (session('status'))
    <div class="notice">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="errors">{{ $errors->first() }}</div>
@endif

<section class="panel">
    <form method="post" action="{{ route('profile.update') }}" class="form">
        @csrf
        @method('put')
        <label>Nome
            <input name="name" value="{{ old('name', $user->name) }}" required>
        </label>
        <label>Email
            <input value="{{ $user->email }}" disabled>
        </label>
        <label>Telefone
            <input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="5585999999999">
        </label>
        <label>ID do WhatsApp
            <input name="whatsapp_id" value="{{ old('whatsapp_id', $user->whatsapp_id) }}">
        </label>
        <button class="button" type="submit">Salvar</button>
    </form>
</section>
@endsection
