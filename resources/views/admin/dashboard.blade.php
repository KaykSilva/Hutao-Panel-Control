@extends('layouts.app', ['title' => 'Admin'])

@section('content')
<div class="topbar">
    <div>
        <h1>Admin</h1>
        <p class="muted">Controle central da plataforma e do bot.</p>
    </div>
    <a class="button" href="{{ route('admin.bot.edit') }}">Configurar bot</a>
</div>

<section class="grid stats">
    <div class="card">
        <div class="muted">Usuarios</div>
        <div class="stat-value">{{ $usersCount }}</div>
    </div>
    <div class="card">
        <div class="muted">Contatos do bot</div>
        <div class="stat-value">{{ $contactsCount }}</div>
    </div>
    <div class="card">
        <div class="muted">Mensagens</div>
        <div class="stat-value">{{ $messagesCount }}</div>
    </div>
</section>

<section class="panel" style="margin-top:16px">
    <h2>Atividade recente</h2>
    <table class="table">
        <thead>
            <tr><th>Data</th><th>Contato</th><th>Direcao</th><th>Mensagem</th></tr>
        </thead>
        <tbody>
        @forelse($latestMessages as $message)
            <tr>
                <td>{{ $message->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $message->contact?->phone }}</td>
                <td>{{ $message->direction }}</td>
                <td>{{ $message->content }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="muted">Nenhuma mensagem registrada.</td></tr>
        @endforelse
        </tbody>
    </table>
</section>
@endsection
