@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
<div class="topbar">
    <div>
        <h1>Dashboard</h1>
        <p class="muted">Bem-vindo, {{ $user->name }}.</p>
    </div>
    <a class="button secondary" href="{{ route('profile.edit') }}">Editar perfil</a>
</div>

<section class="panel">
    <h2>Conta</h2>
    <div class="grid stats">
        <div class="card">
            <div class="muted">Email</div>
            <strong>{{ $user->email }}</strong>
        </div>
        <div class="card">
            <div class="muted">Telefone</div>
            <strong>{{ $user->phone ?: 'Nao informado' }}</strong>
        </div>
        <div class="card">
            <div class="muted">WhatsApp</div>
            <strong>{{ $user->whatsapp_id ?: 'Nao vinculado' }}</strong>
        </div>
    </div>
</section>

<section class="panel" style="margin-top:16px">
    <h2>Ultimas mensagens no bot</h2>
    <table class="table">
        <thead>
            <tr><th>Data</th><th>Direcao</th><th>Mensagem</th></tr>
        </thead>
        <tbody>
        @forelse($messages as $message)
            <tr>
                <td>{{ $message->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $message->direction }}</td>
                <td>{{ $message->content }}</td>
            </tr>
        @empty
            <tr><td colspan="3" class="muted">Nenhuma mensagem encontrada.</td></tr>
        @endforelse
        </tbody>
    </table>
</section>
@endsection
